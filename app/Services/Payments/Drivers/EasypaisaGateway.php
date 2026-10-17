<?php

namespace App\Services\Payments\Drivers;

use App\Contracts\PaymentGateway;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Services\Payments\Concerns\RecordsWebhookEvents;
use App\Services\Payments\InvoiceService;
use App\Services\Payments\PaymentLedgerService;
use App\Services\Payments\PaymentStatusMachine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EasypaisaGateway implements PaymentGateway
{
    use RecordsWebhookEvents;

    public function __construct(
        private readonly InvoiceService $invoices,
        private readonly PaymentStatusMachine $statuses,
        private readonly PaymentLedgerService $ledger,
    ) {}

    public function name(): string
    {
        return 'easypaisa';
    }

    public function initiate(Payment $payment, array $payload = []): array
    {
        abort_unless(config('payments.easypaisa.enabled'), 422, 'Easypaisa payments are disabled.');

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
        if (! config('payments.easypaisa.enabled')) {
            return false;
        }

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
        if (! config('payments.easypaisa.enabled')) {
            throw ValidationException::withMessages(['gateway' => 'Easypaisa is disabled.']);
        }

        $this->assertFreshTimestamp($payload);

        $eventId = (string) ($payload['orderId'] ?? $payload['event_id'] ?? '');
        if ($eventId === '') {
            throw ValidationException::withMessages(['event_id' => 'Missing order id.']);
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

            $existingEvent = PaymentWebhookEvent::query()
                ->where('gateway', $this->name())
                ->where('event_id', $eventId)
                ->first();

            if ($existingEvent || ! $this->statuses->webhookMayUpdate($payment)) {
                return $payment;
            }

            $amount = $this->normalizeAmount($payload['transactionAmount'] ?? $payload['amount'] ?? null);
            $currency = strtoupper((string) ($payload['currency'] ?? $payment->currency));

            if ($amount === null
                || bccomp($amount, number_format((float) $payment->amount, 2, '.', ''), 2) !== 0
                || $currency !== strtoupper((string) $payment->currency)) {
                Log::warning('Easypaisa webhook amount mismatch', [
                    'external_id' => $eventId,
                    'expected' => $payment->amount,
                    'got' => $amount,
                ]);
                throw ValidationException::withMessages([
                    'amount' => 'Webhook amount/currency does not match payment.',
                ]);
            }

            $race = $this->recordWebhookEventOrExisting($this->name(), $eventId, $payload, $payment);
            if ($race) {
                return $race;
            }

            $status = strtolower((string) ($payload['transactionStatus'] ?? $payload['status'] ?? ''));

            if (in_array($status, ['paid', 'success', 'completed'], true)) {
                $this->statuses->transition($payment, 'paid');
                $paid = $payment->fresh();
                $this->invoices->createFor($paid);
                if ($paid->payable_type === Booking::class && $paid->payable instanceof Booking) {
                    $this->ledger->markBookingPaid($paid->payable, $paid);
                }
            } else {
                $this->statuses->transition($payment, 'failed');
            }

            return $payment->fresh();
        });
    }

    private function assertFreshTimestamp(array $payload): void
    {
        $raw = $payload['timestamp'] ?? $payload['txnDateTime'] ?? null;
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
