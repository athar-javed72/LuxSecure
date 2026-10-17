<?php

namespace App\Services\Payments\Drivers;

use App\Contracts\PaymentGateway;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Services\Payments\InvoiceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class JazzCashGateway implements PaymentGateway
{
    public function __construct(private readonly InvoiceService $invoices) {}

    public function name(): string
    {
        return 'jazzcash';
    }

    public function initiate(Payment $payment, array $payload = []): array
    {
        $merchantId = config('payments.jazzcash.merchant_id');
        $txnRef = 'JC-'.$payment->uuid;

        $payment->update([
            'external_id' => $txnRef,
            'status' => 'pending',
            'meta' => array_merge($payment->meta ?? [], [
                'sandbox' => (bool) config('payments.jazzcash.sandbox'),
                'merchant_id' => $merchantId,
                'return_url' => config('payments.jazzcash.return_url'),
            ]),
        ]);

        return [
            'payment_uuid' => $payment->uuid,
            'external_id' => $txnRef,
            'checkout_url' => config('payments.jazzcash.sandbox_url'),
            'status' => $payment->status,
        ];
    }

    public function verifyWebhook(array $payload, ?string $signature = null): bool
    {
        $secret = (string) config('payments.jazzcash.integrity_salt');
        if ($secret === '') {
            return false;
        }

        $provided = $signature ?: ($payload['pp_SecureHash'] ?? $payload['signature'] ?? null);
        if (! is_string($provided) || $provided === '') {
            return false;
        }

        $data = $payload;
        unset($data['pp_SecureHash'], $data['signature']);
        ksort($data);
        $computed = strtoupper(hash_hmac('sha256', implode('&', $data), $secret));

        return hash_equals($computed, strtoupper($provided));
    }

    public function handleWebhook(array $payload): Payment
    {
        $eventId = (string) ($payload['pp_TxnRefNo'] ?? $payload['event_id'] ?? '');
        if ($eventId === '') {
            throw ValidationException::withMessages(['event_id' => 'Missing transaction reference.']);
        }

        if (PaymentWebhookEvent::query()->where('gateway', $this->name())->where('event_id', $eventId)->exists()) {
            $payment = Payment::query()->where('external_id', $eventId)->firstOrFail();

            return $payment;
        }

        PaymentWebhookEvent::create([
            'gateway' => $this->name(),
            'event_id' => $eventId,
            'payload' => $payload,
            'status' => 'processed',
        ]);

        $payment = Payment::query()->where('external_id', $eventId)->firstOrFail();
        $responseCode = (string) ($payload['pp_ResponseCode'] ?? $payload['status'] ?? '');

        if (in_array($responseCode, ['000', 'success', 'paid'], true)) {
            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            $this->invoices->createFor($payment);
        } else {
            $payment->update(['status' => 'failed']);
            Log::info('JazzCash payment failed', ['external_id' => $eventId, 'code' => $responseCode]);
        }

        return $payment->fresh();
    }
}
