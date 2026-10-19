<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Country;
use App\Models\Payment;
use App\Models\Tenancy;
use Illuminate\Support\Facades\DB;

class CountryVisibility
{
    /**
     * @return array{blocked: bool, reasons: list<string>}
     */
    public static function disableBlockers(Country $country): array
    {
        $reasons = [];

        $activeBookings = Booking::query()
            ->where('country_id', $country->id)
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->count();
        if ($activeBookings > 0) {
            $reasons[] = "{$activeBookings} active booking(s)";
        }

        $activeTenancies = Tenancy::query()
            ->where('status', 'active')
            ->whereHas('listing', fn ($q) => $q->where('country_id', $country->id))
            ->count();
        if ($activeTenancies > 0) {
            $reasons[] = "{$activeTenancies} active tenanc(ies)";
        }

        $unpaid = Payment::query()
            ->where('country_id', $country->id)
            ->whereIn('status', ['pending', 'awaiting_approval', 'refund_pending'])
            ->count();
        if ($unpaid > 0) {
            $reasons[] = "{$unpaid} unpaid / pending payment(s)";
        }

        return [
            'blocked' => $reasons !== [],
            'reasons' => $reasons,
        ];
    }

    /**
     * @param  list<int>  $enableIds
     * @return array{activated: list<int>, deactivated: list<int>, wind_down: list<int>, errors: array<int, string>}
     */
    public static function sync(array $enableIds, bool $confirmWindDown = false): array
    {
        $enableIds = array_values(array_unique(array_map('intval', $enableIds)));
        $result = [
            'activated' => [],
            'deactivated' => [],
            'wind_down' => [],
            'errors' => [],
        ];

        DB::transaction(function () use ($enableIds, $confirmWindDown, &$result) {
            $countries = Country::query()->orderBy('id')->get();

            foreach ($countries as $country) {
                $shouldEnable = in_array((int) $country->id, $enableIds, true);

                if ($shouldEnable) {
                    if ($country->is_active && ! $country->wind_down) {
                        continue;
                    }

                    $readiness = CountryReadiness::check($country);
                    if (! $readiness['ready']) {
                        $result['errors'][$country->id] = $country->name.': missing '.implode(', ', $readiness['missing']);

                        continue;
                    }

                    $country->forceFill([
                        'is_active' => true,
                        'wind_down' => false,
                    ])->save();
                    $result['activated'][] = $country->id;
                    activity()
                        ->performedOn($country)
                        ->withProperties(['event' => 'country.activated'])
                        ->log('Country activated: '.$country->name);

                    continue;
                }

                // Disable / wind-down path
                if (! $country->is_active && ! $country->wind_down) {
                    continue;
                }

                $blockers = self::disableBlockers($country);
                if ($blockers['blocked']) {
                    if (! $confirmWindDown) {
                        $result['errors'][$country->id] = $country->name.': cannot disable ('.implode('; ', $blockers['reasons']).'). Confirm wind-down to proceed.';

                        continue;
                    }

                    $country->forceFill([
                        'is_active' => true,
                        'wind_down' => true,
                    ])->save();
                    $result['wind_down'][] = $country->id;
                    activity()
                        ->performedOn($country)
                        ->withProperties([
                            'event' => 'country.wind_down',
                            'reasons' => $blockers['reasons'],
                        ])
                        ->log('Country entered wind-down: '.$country->name);

                    continue;
                }

                $country->forceFill([
                    'is_active' => false,
                    'wind_down' => false,
                ])->save();
                $result['deactivated'][] = $country->id;
                activity()
                    ->performedOn($country)
                    ->withProperties(['event' => 'country.deactivated'])
                    ->log('Country deactivated: '.$country->name);
            }
        });

        CountryCatalog::flush();

        return $result;
    }
}
