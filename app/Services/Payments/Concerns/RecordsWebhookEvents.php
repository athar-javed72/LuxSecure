<?php

namespace App\Services\Payments\Concerns;

use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use Illuminate\Database\QueryException;

trait RecordsWebhookEvents
{
    /**
     * Insert a webhook event; on unique race, return the existing payment (no-op).
     */
    protected function recordWebhookEventOrExisting(
        string $gateway,
        string $eventId,
        array $payload,
        Payment $payment,
    ): ?Payment {
        try {
            PaymentWebhookEvent::create([
                'gateway' => $gateway,
                'event_id' => $eventId,
                'payload' => $payload,
                'status' => 'processed',
            ]);
        } catch (QueryException $e) {
            if ($this->isUniqueViolation($e)) {
                return Payment::query()->where('external_id', $eventId)->first() ?? $payment->fresh();
            }

            throw $e;
        }

        return null;
    }

    protected function isUniqueViolation(QueryException $e): bool
    {
        $code = (string) ($e->errorInfo[1] ?? $e->getCode());

        // MySQL 1062, Postgres 23505, SQLite SQLITE_CONSTRAINT
        return in_array($code, ['1062', '23505', '19'], true)
            || str_contains(strtolower($e->getMessage()), 'unique');
    }
}
