<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesListings;
use Tests\TestCase;

class PropertyListingTest extends TestCase
{
    use CreatesListings;
    use RefreshDatabase;

    public function test_properties_page_returns_200(): void
    {
        $response = $this->get(route('properties'));
        $response->assertStatus(200);
    }

    public function test_properties_page_shows_properties_from_database(): void
    {
        $this->makeListing([
            'title' => 'Test House',
            'location' => 'Karachi',
        ]);

        $response = $this->get(route('properties'));
        $response->assertStatus(200);
        $response->assertSee('Test House');
        $response->assertSee('Karachi');
    }
}
