<?php

namespace App\Services\Payments\Drivers;

use App\Contracts\PaymentGateway;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Services\Payments\InvoiceService;
use App\Services\Payments\PaymentStatusMachine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class JazzCashGateway implements PaymentGateway
{
    public function __construct(
        private readonly InvoiceService $invoices,
        private readonly PaymentStatusMachine $statuses,
    ) {}

    public function name(): string
    {
        return 'jazzcash';
    }

    public function initiate(Payment $payment, array $payload = []): array
    {
        abort_unless(config('payments.jazzcash.enabled'), 422, 'JazzCash payments are disabled.');

        $txnRef = 'JC-'.$payment->uuid;

        $payment->update([
            'external_id' => $txnRef,
            'status' => 'pending',
            'meta' => array_merge($payment->meta ?? [], [
                'sandbox' => (bool) config('payments.jazzcash.sandbox'),
                'merchant_id' => config('payments.jazzcash.merchant_id'),
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
        if (! config('payments.jazzcash.enabled')) {
            return false;
        }

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
        if (! config('payments.jazzcash.enabled')) {
            throw ValidationException::withMessages(['gateway' => 'JazzCash is disabled.']);
        }

        $this->assertFreshTimestamp($payload);

        $eventId = (string) ($payload['pp_TxnRefNo'] ?? $payload['event_id'] ?? '');
        if ($eventId === '') {
            throw ValidationException::withMessages(['event_id' => 'Missing transaction reference.']);
        }

        return DB::transaction(function () use ($payload, $eventId) {
            $existingEvent = PaymentWebhookEvent::query()
                ->where('gateway', $this->name())
                ->where('event_id', $eventId)
                ->lockForUpdate()
                ->first();

            if ($existingEvent) {
                return Payment::query()->where('external_id', $eventId)->lockForUpdate()->firstOrFail();
            }

            $payment = Payment::query()->where('external_id', $eventId)->lockForUpdate()->firstOrFail();

            // Concurrent winner may have inserted the event while we waited on the payment row.
            $existingEvent = PaymentWebhookEvent::query()
                ->where('gateway', $this->name())
                ->where('event_id', $eventId)
                ->first();

            if ($existingEvent || ! $this->statuses->webhookMayUpdate($payment)) {
                return $payment;
            }

            $amount = $this->normalizeAmount($payload['pp_Amount'] ?? $payload['amount'] ?? null);
            $currency = strtoupper((string) ($payload['pp_Currency'] ?? $payload['currency'] ?? $payment->currency));

            if ($amount === null
                || bccomp($amount, number_format((float) $payment->amount, 2, '.', ''), 2) !== 0
                || $currency !== strtoupper((string) $payment->currency)) {
                Log::warning('JazzCash webhook amount mismatch', [
                    'external_id' => $eventId,
                    'expected' => $payment->amount,
                    'got' => $amount,
                    'currency' => $currency,
                ]);
                throw ValidationException::withMessages([
                    'amount' => 'Webhook amount/currency does not match payment.',
                ]);
            }

            PaymentWebhookEvent::create([
                'gateway' => $this->name(),
                'event_id' => $eventId,
                'payload' => $payload,
                'status' => 'processed',
            ]);

            $responseCode = (string) ($payload['pp_ResponseCode'] ?? $payload['status'] ?? '');

            if (in_array($responseCode, ['000', 'success', 'paid'], true)) {
                $this->statuses->transition($payment, 'paid');
                $this->invoices->createFor($payment->fresh());
            } else {
                $this->statuses->transition($payment, 'failed');
            }

            return $payment->fresh();
        });
    }

    private function assertFreshTimestamp(array $payload): void
    {
        $raw = $payload['timestamp'] ?? $payload['pp_TxnDateTime'] ?? $payload['txnDateTime'] ?? null;
        if (! is_string($raw) && ! is_numeric($raw)) {
            throw ValidationException::withMessages(['timestamp' => 'Missing webhook timestamp.']);
        }

        try {
            $ts = is_numeric($raw)
                ? Carbon::createFromTimestamp((int) $raw)
                : Carbon::parse((string) $raw);
        } catch (\Throwable) {
            throw ValidationException::withMessages(['timestamp' => 'Invalid webhook timestamp.']);
        }

        if ($ts->lt(now()->subMinutes(5)) || $ts->gt(now()->addMinute())) {
            throw ValidationException::withMessages(['timestamp' => 'Webhook timestamp expired.']);
        }
    }

    private function normalizeAmount(mixed $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_string($raw) && ! is_numeric($raw)) {
            return null;
        }

        return number_format((float) $raw, 2, '.', '');
    }
}
