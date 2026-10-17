<?php

namespace App\Services\Payments;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentLedgerService
{
    public function __construct(
        private readonly PaymentStatusMachine $statuses,
        private readonly RefundCalculator $refunds,
    ) {}

    public function createPendingForBooking(Booking $booking, string $gateway = 'bank_transfer'): Payment
    {
        return DB::transaction(function () use ($booking, $gateway) {
            return Payment::query()->create([
                'payable_type' => Booking::class,
                'payable_id' => $booking->id,
                'user_id' => $booking->user_id,
                'country_id' => $booking->country_id,
                'amount' => $booking->total,
                'currency' => $booking->currency ?: ($booking->listing?->currency ?: 'PKR'),
                'gateway' => $gateway,
                'status' => 'pending',
                'meta' => ['source' => 'booking_create'],
            ]);
        });
    }

    public function markBookingPaid(Booking $booking, Payment $payment): void
    {
        DB::transaction(function () use ($booking, $payment) {
            $locked = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();
            if ($payment->status === 'paid' && $locked->status === 'pending') {
                $locked->forceFill([
                    'status' => 'confirmed',
                    'hold_expires_at' => null,
                ])->save();
            }
        });
    }

    public function clearHold(Booking $booking): void
    {
        $booking->forceFill(['hold_expires_at' => null])->save();
    }

    /**
     * Cancel a booking and apply ledger transitions for related payments.
     *
     * @return array{booking: Booking, refunds: list<array<string, mixed>>}
     */
    public function cancelBooking(
        Booking $booking,
        ?User $actor = null,
        bool $adminOverride = false,
        ?string $overrideReason = null,
    ): array {
        return DB::transaction(function () use ($booking, $actor, $adminOverride, $overrideReason) {
            $locked = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if (! in_array($locked->status, ['pending', 'confirmed'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Booking cannot be cancelled.',
                ]);
            }

            $locked->forceFill([
                'status' => 'cancelled',
                'hold_expires_at' => null,
            ])->save();

            $results = [];
            $payments = Payment::query()
                ->where('payable_type', Booking::class)
                ->where('payable_id', $locked->id)
                ->lockForUpdate()
                ->get();

            foreach ($payments as $payment) {
                $results[] = $this->applyCancelToPayment($payment, $locked, $actor, $adminOverride, $overrideReason);
            }

            return ['booking' => $locked->fresh(), 'refunds' => $results];
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function applyCancelToPayment(
        Payment $payment,
        Booking $booking,
        ?User $actor,
        bool $adminOverride,
        ?string $overrideReason,
    ): array {
        if (in_array($payment->status, ['pending', 'awaiting_approval'], true)) {
            $this->statuses->transition($payment, 'failed');
            $payment->update([
                'meta' => array_merge($payment->fresh()->meta ?? [], [
                    'voided' => true,
                    'void_reason' => 'booking_cancelled',
                ]),
            ]);

            return ['payment_id' => $payment->id, 'action' => 'void_pending', 'amount' => '0.00'];
        }

        if ($payment->status !== 'paid' && $payment->status !== 'refund_pending') {
            return ['payment_id' => $payment->id, 'action' => 'none', 'amount' => '0.00'];
        }

        $calc = $this->refunds->forBooking($booking, (float) $payment->amount);

        if ($adminOverride) {
            $calc = [
                'action' => 'full',
                'refund_percent' => 100.0,
                'refund_amount' => number_format((float) $payment->amount, 2, '.', ''),
                'reason' => $overrideReason ?: 'Admin override refund.',
                'days_before_check_in' => $calc['days_before_check_in'],
            ];
        }

        if ($calc['action'] === 'none') {
            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'cancel_refund' => $calc,
                    'refund_denied' => true,
                ]),
            ]);

            return ['payment_id' => $payment->id, ...$calc];
        }

        if ($payment->status === 'paid') {
            $this->statuses->transition($payment, 'refund_pending');
            $payment->refresh();
        }

        $payment->update([
            'meta' => array_merge($payment->meta ?? [], [
                'cancel_refund' => $calc,
                'refund_requested_by' => $actor?->id,
            ]),
        ]);

        return ['payment_id' => $payment->id, ...$calc];
    }

    public function adminRefund(Payment $payment, User $actor, ?string $reason = null): Payment
    {
        return DB::transaction(function () use ($payment, $actor, $reason) {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if (! in_array($locked->status, ['paid', 'refund_pending'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Only paid or refund-pending payments can be refunded.',
                ]);
            }

            if ($locked->status === 'paid') {
                $this->statuses->transition($locked, 'refund_pending');
                $locked->refresh();
            }

            $this->statuses->transition($locked, 'refunded');
            $locked->update([
                'meta' => array_merge($locked->meta ?? [], [
                    'refunded_at' => now()->toIso8601String(),
                    'refunded_by' => $actor->id,
                    'refund_reason' => $reason,
                    'admin_override' => true,
                    'refund_amount' => number_format((float) $locked->amount, 2, '.', ''),
                ]),
            ]);

            activity()
                ->performedOn($locked)
                ->causedBy($actor)
                ->withProperties([
                    'status' => 'refunded',
                    'amount' => $locked->amount,
                    'reason' => $reason,
                ])
                ->log('payment_refunded');

            return $locked->fresh();
        });
    }

    public function voidPendingForPayable(Booking|RentPayment $payable): void
    {
        DB::transaction(function () use ($payable) {
            $payments = Payment::query()
                ->where('payable_type', $payable::class)
                ->where('payable_id', $payable->id)
                ->whereIn('status', ['pending', 'awaiting_approval'])
                ->lockForUpdate()
                ->get();

            foreach ($payments as $payment) {
                $this->statuses->transition($payment, 'failed');
                $payment->update([
                    'meta' => array_merge($payment->fresh()->meta ?? [], ['voided' => true]),
                ]);
            }
        });
    }
}
