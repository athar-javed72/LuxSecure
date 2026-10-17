<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesListings;
use Tests\TestCase;

class PropertyApiTest extends TestCase
{
    use CreatesListings;
    use RefreshDatabase;

    public function test_api_properties_returns_200(): void
    {
        $response = $this->getJson('/api/properties');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'meta']);
    }

    public function test_api_properties_includes_property_data(): void
    {
        $this->makeListing([
            'title' => 'API Test Property',
            'location' => 'Lahore',
            'price' => 100000000,
        ]);

        $response = $this->getJson('/api/properties');
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'API Test Property']);
    }
}
