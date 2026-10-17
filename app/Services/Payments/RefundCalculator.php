<?php

namespace App\Services\Payments;

use App\Models\Booking;
use Carbon\Carbon;

class RefundCalculator
{
    /**
     * @return array{
     *     action: 'void_pending'|'full'|'partial'|'none',
     *     refund_percent: float,
     *     refund_amount: string,
     *     reason: string,
     *     days_before_check_in: int
     * }
     */
    public function forBooking(Booking $booking, float $paidAmount): array
    {
        $checkIn = Carbon::parse($booking->check_in)->startOfDay();
        $days = (int) now()->startOfDay()->diffInDays($checkIn, false);
        $policy = $booking->cancellation_policy ?: 'flexible';

        $percent = match ($policy) {
            'flexible' => $days >= 1 ? 100.0 : 0.0,
            'moderate' => $days >= 5 ? 100.0 : ($days >= 1 ? 50.0 : 0.0),
            'strict' => $days >= 14 ? 100.0 : 0.0,
            default => $days >= 1 ? 100.0 : 0.0,
        };

        $amount = number_format(round($paidAmount * ($percent / 100), 2), 2, '.', '');

        $action = match (true) {
            $percent >= 100.0 => 'full',
            $percent > 0.0 => 'partial',
            default => 'none',
        };

        return [
            'action' => $action,
            'refund_percent' => $percent,
            'refund_amount' => $amount,
            'reason' => "Policy [{$policy}] with {$days} day(s) before check-in.",
            'days_before_check_in' => $days,
        ];
    }
}
