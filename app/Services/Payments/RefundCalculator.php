<?php

namespace App\Services\Payments;

use App\Models\Booking;
use App\Models\CancellationPolicy;
use App\Models\Currency;
use Carbon\Carbon;

class RefundCalculator
{
    /**
     * @return array{
     *     action: 'void_pending'|'full'|'partial'|'none',
     *     refund_percent: float,
     *     refund_amount: string,
     *     reason: string,
     *     days_before_check_in: int,
     *     fee_fixed: float|null
     * }
     */
    public function forBooking(Booking $booking, float $paidAmount): array
    {
        $tz = $booking->listing?->timezone
            ?: $booking->country?->timezone
            ?: config('app.timezone');

        $checkIn = Carbon::parse($booking->check_in, $tz)->startOfDay();
        $days = (int) now($tz)->startOfDay()->diffInDays($checkIn, false);

        $policy = $this->resolvePolicy($booking);
        $rule = $this->matchRule($policy, $days);

        $percent = (float) ($rule['refund_percent'] ?? 0);
        $fee = isset($rule['fee_fixed']) && $rule['fee_fixed'] !== null ? (float) $rule['fee_fixed'] : 0.0;
        $decimals = (int) (Currency::query()
            ->where('code', strtoupper((string) ($booking->currency ?: 'PKR')))
            ->value('decimals') ?? 2);
        $raw = max(0, round($paidAmount * ($percent / 100) - $fee, $decimals));
        $amount = number_format($raw, $decimals, '.', '');

        $action = match (true) {
            $percent >= 100.0 && $fee <= 0 => 'full',
            $raw > 0.0 => 'partial',
            default => 'none',
        };

        $policyName = $policy?->name ?? $policy?->slug ?? 'unknown';

        return [
            'action' => $action,
            'refund_percent' => $percent,
            'refund_amount' => $amount,
            'reason' => "Policy [{$policyName}] with {$days} day(s) before check-in.",
            'days_before_check_in' => $days,
            'fee_fixed' => $fee > 0 ? $fee : null,
        ];
    }

    /**
     * Preview table rows for admin UI (days before → refund %).
     *
     * @return array<int, array{days_before: int, refund_percent: float, fee_fixed: float|null}>
     */
    public function previewTable(CancellationPolicy $policy, int $maxDays = 30): array
    {
        $policy->loadMissing('rules');
        $rows = [];
        for ($d = $maxDays; $d >= 0; $d--) {
            $rule = $this->matchRule($policy, $d);
            $rows[] = [
                'days_before' => $d,
                'refund_percent' => (float) ($rule['refund_percent'] ?? 0),
                'fee_fixed' => isset($rule['fee_fixed']) ? (float) $rule['fee_fixed'] : null,
            ];
        }

        return $rows;
    }

    private function resolvePolicy(Booking $booking): ?CancellationPolicy
    {
        // Prefer explicit policy id when present.
        if ($booking->cancellation_policy_id) {
            $direct = CancellationPolicy::query()->with('rules')->find($booking->cancellation_policy_id);
            // If legacy slug was changed without updating FK, honour the slug for global policies.
            if ($direct && (blank($booking->cancellation_policy) || $direct->slug === $booking->cancellation_policy)) {
                return $direct;
            }
        }

        if (filled($booking->cancellation_policy)) {
            $bySlug = CancellationPolicy::query()
                ->with('rules')
                ->where('slug', $booking->cancellation_policy)
                ->whereNull('country_id')
                ->whereNull('organization_id')
                ->first();
            if ($bySlug) {
                return $bySlug;
            }
        }

        if ($booking->cancellation_policy_id) {
            $direct = CancellationPolicy::query()->with('rules')->find($booking->cancellation_policy_id);
            if ($direct) {
                return $direct;
            }
        }

        $listing = $booking->listing;
        if ($listing) {
            return CancellationPolicy::resolveForListing($listing);
        }

        return CancellationPolicy::query()
            ->with('rules')
            ->whereNull('country_id')
            ->whereNull('organization_id')
            ->where('is_default', true)
            ->first();
    }

    /**
     * @return array{refund_percent: float|int|string, fee_fixed: float|int|string|null}|null
     */
    private function matchRule(?CancellationPolicy $policy, int $days): ?array
    {
        if (! $policy) {
            return ['refund_percent' => 0, 'fee_fixed' => null];
        }

        $rules = $policy->relationLoaded('rules')
            ? $policy->rules
            : $policy->rules()->get();

        $sorted = $rules->sortByDesc('days_before_min');
        foreach ($sorted as $rule) {
            if ($days >= (int) $rule->days_before_min) {
                return [
                    'refund_percent' => $rule->refund_percent,
                    'fee_fixed' => $rule->fee_fixed,
                ];
            }
        }

        return ['refund_percent' => 0, 'fee_fixed' => null];
    }
}
