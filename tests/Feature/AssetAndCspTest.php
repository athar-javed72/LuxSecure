<?php

namespace Tests\Feature;

use App\Models\Listing;
use Database\Seeders\AdminAndPropertiesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class AssetAndCspTest extends TestCase
{
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();

        // Prefer built assets in tests (ignore a leftover Vite HMR hot file).
        if (File::exists(public_path('hot'))) {
            File::delete(public_path('hot'));
        }
    }

    public function test_properties_page_renders_stylesheet_link_and_allows_self_assets_in_csp(): void
    {
        $this->assertFileExists(public_path('build/manifest.json'));

        $response = $this->get(route('properties'));

        $response->assertOk();
        $response->assertSee('rel="stylesheet"', false);
        $response->assertSee('/build/assets/', false);

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertNotSame('', $csp);
        $this->assertMatchesRegularExpression("/style-src[^;]*'self'/", $csp);
        $this->assertMatchesRegularExpression("/script-src[^;]*'self'/", $csp);
        $this->assertStringContainsString('style-src', $csp);
        $this->assertStringContainsString("'unsafe-inline'", $csp);
    }

    public function test_home_login_and_admin_include_built_css_link(): void
    {
        $this->seedRbac();
        $admin = $this->makeAdmin();

        foreach ([route('home'), route('login.form')] as $url) {
            $response = $this->get($url);
            $response->assertOk();
            $response->assertSee('rel="stylesheet"', false);
            $response->assertSee('/build/assets/', false);
        }

        $adminResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $adminResponse->assertOk();
        $adminResponse->assertSee('rel="stylesheet"', false);
        $adminResponse->assertSee('/build/assets/', false);
    }

    public function test_properties_filter_defaults_to_all_types_and_seeded_listings_are_visible(): void
    {
        $this->seed(AdminAndPropertiesSeeder::class);

        $response = $this->get(route('properties'));

        $response->assertOk();
        // Toolbar / filters (Estate Noir properties UI) — not the legacy "All types" select.
        $response->assertSee('Type', false);
        $response->assertSee('More filters', false);
        $response->assertSee('Gulberg Apartment for Sale');
        $this->assertGreaterThan(0, Listing::published()->count());
    }
}
