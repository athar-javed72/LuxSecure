<?php

namespace Tests\Feature;

use App\Support\BrandAssets;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_svg_kit_exists_and_paths_resolve(): void
    {
        foreach ([
            BrandAssets::logo('horizontal', 'dark'),
            BrandAssets::logo('horizontal', 'light'),
            BrandAssets::logo('compact', 'dark'),
            BrandAssets::logo('compact', 'light'),
            BrandAssets::logo('stacked', 'dark'),
            BrandAssets::logo('monogram-nostar', 'dark'),
            BrandAssets::logo('stacked', 'dark', 'hotels'),
            BrandAssets::logo('stacked', 'light', 'properties'),
            BrandAssets::logo('stacked', 'dark', 'living'),
            BrandAssets::favicon(),
        ] as $path) {
            $this->assertFileExists(public_path(ltrim($path, '/')));
        }

        $this->assertFileExists(public_path('favicon-32x32.png'));
        $this->assertFileExists(public_path('apple-touch-icon.png'));
        $this->assertFileExists(public_path('android-chrome-192x192.png'));
        $this->assertFileExists(public_path('android-chrome-512x512.png'));
        $this->assertFileExists(public_path('brand/og-image.png'));
    }

    public function test_division_pages_render_without_concept_logo_copy(): void
    {
        $this->get(route('divisions.hospitality'))
            ->assertOk()
            ->assertSee('Maskanoor Group', false)
            ->assertSee('/brand/divisions/maskanoor-hotels-for-dark-bg.svg', false)
            ->assertDontSee('concept logo', false);

        $this->get(route('divisions.living'))
            ->assertOk()
            ->assertSee('/brand/divisions/maskanoor-living-for-dark-bg.svg', false);
    }

    public function test_home_uses_brand_favicon_and_compact_lockup(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('/brand/favicon/favicon.svg', false)
            ->assertSee('/brand/logos/maskanoor-group-horizontal-compact-for-dark-bg.svg', false)
            ->assertDontSee('concept logo', false)
            ->assertDontSee('Enterprise-grade security', false)
            ->assertDontSee('Equal tiles for every category', false);
    }
}
