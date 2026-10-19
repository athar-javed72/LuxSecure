<?php

namespace Tests\Support;

use App\Models\City;
use App\Models\Country;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\TypeAttribute;
use Illuminate\Support\Str;

trait CreatesListings
{
    protected function makeListingType(string $name = 'Apartment', array $attributeDefs = []): ListingType
    {
        $type = ListingType::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'is_active' => true,
        ]);

        if ($attributeDefs === []) {
            $attributeDefs = [
                ['key' => 'bedrooms', 'label' => 'Bedrooms', 'input_type' => 'number', 'is_required' => true, 'is_filterable' => true],
                ['key' => 'bathrooms', 'label' => 'Bathrooms', 'input_type' => 'number', 'is_required' => false, 'is_filterable' => true],
            ];
        }

        foreach ($attributeDefs as $i => $def) {
            TypeAttribute::create([
                'listing_type_id' => $type->id,
                'key' => $def['key'],
                'label' => $def['label'],
                'input_type' => $def['input_type'],
                'options' => $def['options'] ?? null,
                'is_required' => $def['is_required'] ?? false,
                'is_filterable' => $def['is_filterable'] ?? false,
                'sort_order' => $i + 1,
            ]);
        }

        return $type->fresh('attributes');
    }

    protected function makeCity(string $name = 'Lahore'): City
    {
        $countryId = Country::query()->firstOrCreate(
            ['iso2' => 'PK'],
            [
                'name' => 'Pakistan',
                'currency_code' => 'PKR',
                'default_locale' => 'en',
                'timezone' => 'Asia/Karachi',
                'is_active' => true,
            ]
        )->id;

        $city = City::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'is_active' => true, 'country_id' => $countryId]
        );
        if (blank($city->country_id)) {
            $city->forceFill(['country_id' => $countryId])->save();
        }

        return $city->fresh();
    }

    protected function makeListing(array $overrides = []): Listing
    {
        $type = $overrides['listing_type_id'] ?? null
            ? ListingType::find($overrides['listing_type_id'])
            : $this->makeListingType();

        $city = $this->makeCity();
        if (! array_key_exists('country_id', $overrides) || blank($overrides['country_id'])) {
            $overrides['country_id'] = Country::query()->firstOrCreate(
                ['iso2' => 'PK'],
                [
                    'name' => 'Pakistan',
                    'currency_code' => 'PKR',
                    'default_locale' => 'en',
                    'timezone' => 'Asia/Karachi',
                    'phone_code' => '+92',
                    'area_units' => ['sq_ft', 'marla'],
                    'is_active' => true,
                    'sort_order' => 1,
                ]
            )->id;
        }

        return Listing::create(array_merge([
            'listing_type_id' => $type->id,
            'title' => 'Test Listing',
            'slug' => 'test-listing-'.Str::random(6),
            'location' => 'Test Location, '.$city->name,
            'purpose' => 'sale',
            'price' => 50000000,
            'price_unit' => 'total',
            'currency' => 'PKR',
            'status' => 'published',
            'attributes' => ['bedrooms' => 3, 'bathrooms' => 2],
            'city_id' => $city->id,
            'description' => 'Test description',
        ], $overrides));
    }
}
