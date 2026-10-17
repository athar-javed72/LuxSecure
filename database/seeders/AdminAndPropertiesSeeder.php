<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\TypeAttribute;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminAndPropertiesSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@luxsecure.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['super_admin']);

        $lahore = City::firstOrCreate(['slug' => 'lahore'], ['name' => 'Lahore', 'is_active' => true]);
        $karachi = City::firstOrCreate(['slug' => 'karachi'], ['name' => 'Karachi', 'is_active' => true]);
        $islamabad = City::firstOrCreate(['slug' => 'islamabad'], ['name' => 'Islamabad', 'is_active' => true]);

        $apartment = $this->ensureType('Apartment', 'apartment', [
            ['key' => 'bedrooms', 'label' => 'Bedrooms', 'input_type' => 'number', 'is_required' => true, 'is_filterable' => true, 'sort_order' => 1],
            ['key' => 'bathrooms', 'label' => 'Bathrooms', 'input_type' => 'number', 'is_required' => true, 'is_filterable' => true, 'sort_order' => 2],
            ['key' => 'floor', 'label' => 'Floor', 'input_type' => 'number', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 3],
            ['key' => 'furnished', 'label' => 'Furnished', 'input_type' => 'boolean', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 4],
        ]);

        $house = $this->ensureType('House', 'house', [
            ['key' => 'bedrooms', 'label' => 'Bedrooms', 'input_type' => 'number', 'is_required' => true, 'is_filterable' => true, 'sort_order' => 1],
            ['key' => 'bathrooms', 'label' => 'Bathrooms', 'input_type' => 'number', 'is_required' => true, 'is_filterable' => true, 'sort_order' => 2],
            ['key' => 'garage', 'label' => 'Garage', 'input_type' => 'boolean', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 3],
        ]);

        $plot = $this->ensureType('Plot', 'plot', [
            ['key' => 'plot_number', 'label' => 'Plot number', 'input_type' => 'text', 'is_required' => false, 'is_filterable' => false, 'sort_order' => 1],
            ['key' => 'facing', 'label' => 'Facing', 'input_type' => 'select', 'options' => ['North', 'South', 'East', 'West'], 'is_required' => false, 'is_filterable' => true, 'sort_order' => 2],
        ]);

        $hotel = $this->ensureType('Hotel', 'hotel', [
            ['key' => 'star_rating', 'label' => 'Star rating', 'input_type' => 'select', 'options' => ['3', '4', '5'], 'is_required' => true, 'is_filterable' => true, 'sort_order' => 1],
            ['key' => 'rooms', 'label' => 'Rooms', 'input_type' => 'number', 'is_required' => true, 'is_filterable' => true, 'sort_order' => 2],
            ['key' => 'breakfast_included', 'label' => 'Breakfast included', 'input_type' => 'boolean', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 3],
        ]);

        $samples = [
            [
                'title' => 'Gulberg Apartment for Sale',
                'location' => 'Gulberg III, Lahore',
                'listing_type_id' => $apartment->id,
                'purpose' => 'sale',
                'price' => 21000000,
                'price_unit' => 'total',
                'city_id' => $lahore->id,
                'area_size' => 1200,
                'area_unit' => 'sq_ft',
                'attributes' => ['bedrooms' => 2, 'bathrooms' => 2, 'floor' => 5, 'furnished' => true],
                'featured_image' => 'https://images.unsplash.com/photo-1502673530728-f79b4cab31b1?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'DHA Apartment for Rent',
                'location' => 'DHA Phase 5, Karachi',
                'listing_type_id' => $apartment->id,
                'purpose' => 'rent',
                'price' => 180000,
                'price_unit' => 'per_month',
                'city_id' => $karachi->id,
                'area_size' => 1400,
                'area_unit' => 'sq_ft',
                'attributes' => ['bedrooms' => 3, 'bathrooms' => 2, 'floor' => 8, 'furnished' => false],
                'featured_image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Model Town Family House',
                'location' => 'Model Town, Lahore',
                'listing_type_id' => $house->id,
                'purpose' => 'sale',
                'price' => 45000000,
                'price_unit' => 'total',
                'city_id' => $lahore->id,
                'area_size' => 10,
                'area_unit' => 'marla',
                'attributes' => ['bedrooms' => 5, 'bathrooms' => 4, 'garage' => true],
                'featured_image' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Bahria Town Residential Plot',
                'location' => 'Bahria Town, Lahore',
                'listing_type_id' => $plot->id,
                'purpose' => 'sale',
                'price' => 35000000,
                'price_unit' => 'total',
                'city_id' => $lahore->id,
                'area_size' => 10,
                'area_unit' => 'marla',
                'attributes' => ['plot_number' => 'B-214', 'facing' => 'North'],
                'featured_image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Blue Area Boutique Hotel',
                'location' => 'Blue Area, Islamabad',
                'listing_type_id' => $hotel->id,
                'purpose' => 'short_stay',
                'price' => 25000,
                'price_unit' => 'per_night',
                'city_id' => $islamabad->id,
                'area_size' => 8000,
                'area_unit' => 'sq_ft',
                'attributes' => ['star_rating' => '5', 'rooms' => 42, 'breakfast_included' => true],
                'featured_image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($samples as $sample) {
            $slug = Str::slug($sample['title']);
            Listing::updateOrCreate(
                ['slug' => $slug],
                array_merge($sample, [
                    'status' => 'published',
                    'currency' => 'PKR',
                    'slug' => $slug,
                    'owner_id' => $admin->id,
                    'agent_id' => $admin->id,
                    'description' => 'A carefully verified LuxSecure listing with modern finishes and a prime location.',
                ])
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $attributes
     */
    private function ensureType(string $name, string $slug, array $attributes): ListingType
    {
        $type = ListingType::updateOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'icon' => null, 'is_active' => true]
        );

        foreach ($attributes as $attr) {
            TypeAttribute::updateOrCreate(
                ['listing_type_id' => $type->id, 'key' => $attr['key']],
                [
                    'label' => $attr['label'],
                    'input_type' => $attr['input_type'],
                    'options' => $attr['options'] ?? null,
                    'is_required' => $attr['is_required'] ?? false,
                    'is_filterable' => $attr['is_filterable'] ?? false,
                    'sort_order' => $attr['sort_order'] ?? 0,
                ]
            );
        }

        return $type->fresh('attributes');
    }
}
