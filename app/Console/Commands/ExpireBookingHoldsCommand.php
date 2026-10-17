<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\Payments\PaymentLedgerService;
use Illuminate\Console\Command;

class ExpireBookingHoldsCommand extends Command
{
    protected $signature = 'luxsecure:expire-booking-holds';

    protected $description = 'Cancel stale pending booking holds and void their pending payments';

    public function handle(PaymentLedgerService $ledger): int
    {
        $expired = Booking::query()
            ->where('status', 'pending')
            ->whereNotNull('hold_expires_at')
            ->where('hold_expires_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($expired as $booking) {
            $ledger->cancelBooking($booking, null, false, 'Hold expired');
            $count++;
        }

        $this->info("Expired {$count} booking hold(s).");

        return self::SUCCESS;
    }
}
