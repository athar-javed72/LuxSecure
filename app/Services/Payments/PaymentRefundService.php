<?php

namespace App\Services\Payments;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentPayment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Facade over PaymentLedgerService for cancel/refund transitions.
 */
class PaymentRefundService
{
    public function __construct(private readonly PaymentLedgerService $ledger) {}

    public function voidOrMarkRefundPending(Booking|RentPayment $payable): void
    {
        if ($payable instanceof Booking) {
            $this->ledger->cancelBooking($payable, Auth::user());

            return;
        }

        $this->ledger->voidPendingForPayable($payable);

        // Rent: mark paid as refund_pending without booking policy.
        Payment::query()
            ->where('payable_type', RentPayment::class)
            ->where('payable_id', $payable->id)
            ->where('status', 'paid')
            ->each(function (Payment $payment) {
                app(PaymentStatusMachine::class)->transition($payment, 'refund_pending');
            });
    }

    public function refund(Payment $payment, ?string $reason = null): Payment
    {
        /** @var User $actor */
        $actor = Auth::user();

        return $this->ledger->adminRefund($payment, $actor, $reason);
    }
}
