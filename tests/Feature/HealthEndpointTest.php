<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_up_endpoint_reports_ok_checks(): void
    {
        $response = $this->getJson('/up');

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonStructure([
                'status',
                'checks' => [
                    'database' => ['ok', 'detail'],
                    'cache' => ['ok', 'detail'],
                    'queue' => ['ok', 'detail'],
                ],
            ]);
    }
}
