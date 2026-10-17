<?php

namespace App\Services\Payments;

use App\Models\Payment;
use InvalidArgumentException;

class PaymentStatusMachine
{
    /** @var array<string, list<string>> */
    public const TRANSITIONS = [
        'pending' => ['awaiting_approval', 'paid', 'failed'],
        'awaiting_approval' => ['paid', 'failed'],
        'paid' => ['refund_pending', 'refunded'],
        'refund_pending' => ['refunded'],
        'failed' => [],
        'refunded' => [],
    ];

    public function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    public function transition(Payment $payment, string $to): void
    {
        $from = $payment->status;
        if (! $this->canTransition($from, $to)) {
            throw new InvalidArgumentException("Invalid payment transition [{$from} -> {$to}].");
        }

        $attrs = ['status' => $to];
        if ($to === 'paid') {
            $attrs['paid_at'] = $payment->paid_at ?? now();
        }

        $payment->forceFill($attrs)->save();
    }

    public function webhookMayUpdate(Payment $payment): bool
    {
        return ! in_array($payment->status, ['paid', 'refunded', 'refund_pending'], true);
    }
}
