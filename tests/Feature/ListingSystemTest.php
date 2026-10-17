<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\ListingType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesListings;
use Tests\TestCase;

class ListingSystemTest extends TestCase
{
    use CreatesListings;
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_dynamic_attribute_validation_requires_configured_fields(): void
    {
        $admin = $this->admin();
        $type = $this->makeListingType('Condo', [
            ['key' => 'bedrooms', 'label' => 'Bedrooms', 'input_type' => 'number', 'is_required' => true, 'is_filterable' => true],
            ['key' => 'view', 'label' => 'View', 'input_type' => 'select', 'options' => ['Sea', 'City'], 'is_required' => true, 'is_filterable' => true],
        ]);

        $response = $this->actingAs($admin)->post(route('admin.listings.store'), [
            'listing_type_id' => $type->id,
            'title' => 'Missing attrs',
            'location' => 'Karachi',
            'purpose' => 'sale',
            'price' => 1000000,
            'price_unit' => 'total',
            'status' => 'published',
            'attributes' => [
                'bedrooms' => 2,
                // view missing
            ],
        ]);

        $response->assertSessionHasErrors(['attributes.view']);
        $this->assertDatabaseMissing('listings', ['title' => 'Missing attrs']);
    }

    public function test_public_filters_by_type_purpose_city_and_filterable_attribute(): void
    {
        $type = $this->makeListingType('Apartment Filter', [
            ['key' => 'bedrooms', 'label' => 'Bedrooms', 'input_type' => 'number', 'is_required' => false, 'is_filterable' => true],
        ]);
        $city = $this->makeCity('Lahore');
        $otherCity = $this->makeCity('Karachi');

        $match = $this->makeListing([
            'listing_type_id' => $type->id,
            'title' => 'Filter Match Listing',
            'purpose' => 'rent',
            'price' => 150000,
            'price_unit' => 'per_month',
            'city_id' => $city->id,
            'attributes' => ['bedrooms' => 3],
        ]);

        $this->makeListing([
            'listing_type_id' => $type->id,
            'title' => 'Other City Listing',
            'purpose' => 'rent',
            'price' => 150000,
            'price_unit' => 'per_month',
            'city_id' => $otherCity->id,
            'attributes' => ['bedrooms' => 3],
        ]);

        $response = $this->get(route('properties', [
            'type' => $type->slug,
            'purpose' => 'rent',
            'city_id' => $city->id,
            'attr_bedrooms' => 3,
        ]));

        $response->assertOk();
        $response->assertSee('Filter Match Listing');
        $response->assertDontSee('Other City Listing');
        $this->assertTrue(Listing::published()->whereKey($match->id)->exists());
    }

    public function test_admin_can_create_listing_type_with_attributes(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.listing-types.store'), [
            'name' => 'Warehouse',
            'slug' => 'warehouse',
            'is_active' => 1,
            'attributes' => [
                [
                    'key' => 'loading_bays',
                    'label' => 'Loading bays',
                    'input_type' => 'number',
                    'options' => '',
                    'is_required' => 1,
                    'is_filterable' => 1,
                    'sort_order' => 1,
                ],
                [
                    'key' => 'cold_storage',
                    'label' => 'Cold storage',
                    'input_type' => 'boolean',
                    'options' => '',
                    'is_required' => 0,
                    'is_filterable' => 1,
                    'sort_order' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.listing-types.index'));
        $this->assertDatabaseHas('listing_types', ['slug' => 'warehouse', 'name' => 'Warehouse']);

        $type = ListingType::where('slug', 'warehouse')->first();
        $this->assertNotNull($type);
        $this->assertDatabaseHas('type_attributes', [
            'listing_type_id' => $type->id,
            'key' => 'loading_bays',
            'is_required' => 1,
            'is_filterable' => 1,
        ]);
        $this->assertDatabaseHas('type_attributes', [
            'listing_type_id' => $type->id,
            'key' => 'cold_storage',
            'input_type' => 'boolean',
        ]);
    }
}
