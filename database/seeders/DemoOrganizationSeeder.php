<?php

namespace Database\Seeders;

use App\Models\CancellationPolicy;
use App\Models\City;
use App\Models\Country;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo hotel org + branches — local/testing only.
 */
class DemoOrganizationSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $owner = User::query()->where('email', 'admin@luxsecure.com')->first()
            ?? User::query()->role('super_admin')->first();

        $org = Organization::query()->firstOrCreate(
            ['slug' => 'maskanoor-stays'],
            [
                'name' => 'Maskanoor Stays',
                'description' => '[DEMO] Multi-country short-stay brand for local QA.',
                'owner_id' => $owner?->id,
                'country_id' => Country::query()->where('iso2', 'PK')->value('id'),
                'is_active' => true,
            ]
        );

        $hotelType = ListingType::query()->where('slug', 'hotel')->first()
            ?? ListingType::query()->where('name', 'like', '%hotel%')->first()
            ?? ListingType::query()->first();

        $policyId = CancellationPolicy::query()
            ->where('slug', 'hotel-flexible')
            ->whereNull('country_id')
            ->value('id');

        $branches = [
            [
                'iso2' => 'PK',
                'city' => 'Islamabad',
                'slug' => 'maskanoor-stays-islamabad',
                'title' => 'Maskanoor Stays Islamabad',
                'branch_name' => 'Islamabad',
                'timezone' => 'Asia/Karachi',
                'address' => '[DEMO] Blue Area, Islamabad',
            ],
            [
                'iso2' => 'AE',
                'city' => 'Dubai',
                'slug' => 'maskanoor-stays-dubai',
                'title' => 'Maskanoor Stays Dubai',
                'branch_name' => 'Dubai',
                'timezone' => 'Asia/Dubai',
                'address' => '[DEMO] Downtown Dubai',
            ],
            [
                'iso2' => 'MY',
                'city' => 'Kuala Lumpur',
                'slug' => 'maskanoor-stays-kuala-lumpur',
                'title' => 'Maskanoor Stays Kuala Lumpur',
                'branch_name' => 'Kuala Lumpur',
                'timezone' => 'Asia/Kuala_Lumpur',
                'address' => '[DEMO] KLCC, Kuala Lumpur',
            ],
        ];

        foreach ($branches as $branch) {
            $country = Country::query()->where('iso2', $branch['iso2'])->first();
            if (! $country) {
                continue;
            }

            $city = City::query()
                ->where('country_id', $country->id)
                ->where('name', $branch['city'])
                ->first();

            Listing::query()->firstOrCreate(
                ['slug' => $branch['slug']],
                [
                    'owner_id' => $owner?->id,
                    'agent_id' => $owner?->id,
                    'listing_type_id' => $hotelType?->id,
                    'country_id' => $country->id,
                    'organization_id' => $org->id,
                    'city_id' => $city?->id,
                    'title' => $branch['title'],
                    'branch_name' => $branch['branch_name'],
                    'purpose' => 'short_stay',
                    'status' => 'published',
                    'price' => 15000,
                    'price_unit' => 'per_night',
                    'currency' => $country->currency_code,
                    'address' => $branch['address'],
                    'location' => $branch['city'],
                    'timezone' => $branch['timezone'],
                    'check_in_time' => '14:00:00',
                    'check_out_time' => '12:00:00',
                    'cancellation_policy_id' => $policyId,
                    'description' => '[DEMO] Short-stay branch for '.$branch['city'].'.',
                ]
            );
        }
    }
}
