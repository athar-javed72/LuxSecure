<?php

namespace App\Services\Payments;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PaymentRefundService
{
    public function __construct(private readonly PaymentStatusMachine $statuses) {}

    public function voidOrMarkRefundPending(Booking|RentPayment $payable): void
    {
        $payments = Payment::query()
            ->where('payable_type', $payable::class)
            ->where('payable_id', $payable->id)
            ->get();

        foreach ($payments as $payment) {
            if (in_array($payment->status, ['pending', 'awaiting_approval'], true)) {
                $this->statuses->transition($payment, 'failed');
                $payment->update([
                    'meta' => array_merge($payment->fresh()->meta ?? [], ['voided' => true]),
                ]);
            } elseif ($payment->status === 'paid') {
                $this->statuses->transition($payment, 'refund_pending');
            }
        }
    }

    public function refund(Payment $payment): Payment
    {
        if (! in_array($payment->status, ['paid', 'refund_pending'], true)) {
            throw ValidationException::withMessages([
                'status' => 'Only paid or refund-pending payments can be refunded.',
            ]);
        }

        if ($payment->status === 'paid') {
            $this->statuses->transition($payment, 'refund_pending');
            $payment->refresh();
        }

        $this->statuses->transition($payment, 'refunded');
        $payment->update([
            'meta' => array_merge($payment->meta ?? [], [
                'refunded_at' => now()->toIso8601String(),
                'refunded_by' => Auth::id(),
            ]),
        ]);

        activity()
            ->performedOn($payment)
            ->causedBy(Auth::user())
            ->withProperties(['status' => 'refunded', 'amount' => $payment->amount])
            ->log('payment_refunded');

        return $payment->fresh();
    }
}
