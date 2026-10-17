<?php

namespace App\Services\Payments\Drivers;

use App\Contracts\PaymentGateway;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Services\Payments\InvoiceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EasypaisaGateway implements PaymentGateway
{
    public function __construct(private readonly InvoiceService $invoices) {}

    public function name(): string
    {
        return 'easypaisa';
    }

    public function initiate(Payment $payment, array $payload = []): array
    {
        $txnRef = 'EP-'.$payment->uuid;

        $payment->update([
            'external_id' => $txnRef,
            'status' => 'pending',
            'meta' => array_merge($payment->meta ?? [], [
                'sandbox' => (bool) config('payments.easypaisa.sandbox'),
                'store_id' => config('payments.easypaisa.store_id'),
            ]),
        ]);

        return [
            'payment_uuid' => $payment->uuid,
            'external_id' => $txnRef,
            'checkout_url' => config('payments.easypaisa.sandbox_url'),
            'status' => $payment->status,
        ];
    }

    public function verifyWebhook(array $payload, ?string $signature = null): bool
    {
        $secret = (string) config('payments.easypaisa.secret');
        if ($secret === '') {
            return false;
        }

        $provided = $signature ?: ($payload['signature'] ?? null);
        if (! is_string($provided) || $provided === '') {
            return false;
        }

        $data = $payload;
        unset($data['signature']);
        ksort($data);
        $computed = hash_hmac('sha256', json_encode($data), $secret);

        return hash_equals($computed, $provided);
    }

    public function handleWebhook(array $payload): Payment
    {
        $eventId = (string) ($payload['orderId'] ?? $payload['event_id'] ?? '');
        if ($eventId === '') {
            throw ValidationException::withMessages(['event_id' => 'Missing order id.']);
        }

        if (PaymentWebhookEvent::query()->where('gateway', $this->name())->where('event_id', $eventId)->exists()) {
            return Payment::query()->where('external_id', $eventId)->firstOrFail();
        }

        PaymentWebhookEvent::create([
            'gateway' => $this->name(),
            'event_id' => $eventId,
            'payload' => $payload,
            'status' => 'processed',
        ]);

        $payment = Payment::query()->where('external_id', $eventId)->firstOrFail();
        $status = strtolower((string) ($payload['transactionStatus'] ?? $payload['status'] ?? ''));

        if (in_array($status, ['paid', 'success', 'completed'], true)) {
            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            $this->invoices->createFor($payment);
        } else {
            $payment->update(['status' => 'failed']);
            Log::info('Easypaisa payment failed', ['external_id' => $eventId, 'status' => $status]);
        }

        return $payment->fresh();
    }
}
