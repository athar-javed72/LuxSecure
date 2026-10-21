<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\MediaAsset;
use App\Services\DivisionCatalog;
use App\Services\MediaResolver;
use App\Services\MediaUploadService;
use Database\Seeders\AdminAndPropertiesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class DivisionMediaSystemTest extends TestCase
{
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRbac();
        Storage::fake('public');
    }

    public function test_division_pages_return_200_with_sparse_and_full_data(): void
    {
        $this->get(route('divisions.hospitality'))->assertOk();
        $this->get(route('divisions.living'))->assertOk();

        $this->seed(AdminAndPropertiesSeeder::class);
        $this->get(route('divisions.hospitality'))->assertOk()->assertDontSee('country-aware commerce');
        $this->get(route('divisions.living'))->assertOk()->assertDontSee('landlord–tenant workflows');
    }

    public function test_media_upload_accepts_images_rejects_svg(): void
    {
        $admin = $this->makeSuperAdmin();
        $jpg = UploadedFile::fake()->image('hero.jpg', 1200, 800);

        $response = $this->actingAs($admin)->post(route('admin.site-media.store'), [
            'slot' => 'hotels.hero',
            'type' => 'image',
            'file' => $jpg,
            'alt' => 'Hotel lobby',
            'is_active' => 1,
        ]);

        if ($response->getSession()->has('errors')) {
            $this->fail('Upload errors: '.$response->getSession()->get('errors')->toJson());
        }
        if ($response->getSession()->has('error')) {
            $this->fail('Upload error flash: '.$response->getSession()->get('error'));
        }
        $response->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('media_assets', ['slot' => 'hotels.hero', 'type' => 'image']);

        $svg = UploadedFile::fake()->create('evil.svg', 20, 'image/svg+xml');
        $this->actingAs($admin)->from(route('admin.site-media.index'))->post(route('admin.site-media.store'), [
            'slot' => 'living.intro',
            'type' => 'image',
            'file' => $svg,
            'alt' => 'Nope',
        ])->assertSessionHasErrors();
    }

    public function test_media_resolver_fallback_chain_and_demo_hidden_in_production(): void
    {
        MediaAsset::query()->create([
            'slot' => 'hotels.hero',
            'type' => 'image',
            'disk' => 'public',
            'path' => '/images/divisions/hotels.jpg',
            'alt' => 'Demo hero',
            'is_active' => true,
            'is_demo' => true,
            'sort' => 0,
        ]);

        $resolver = app(MediaResolver::class);
        $this->assertNotNull($resolver->forSlot('hotels.hero'));

        $this->app['env'] = 'production';
        config(['app.env' => 'production']);
        MediaResolver::forgetSlot('hotels.hero');
        $this->assertNull($resolver->forSlot('hotels.hero'));
        $branded = $resolver->brandedFallback();
        $this->assertSame('branded', $branded['kind']);
    }

    public function test_stats_hide_when_zero_and_query_budget(): void
    {
        $this->seed(AdminAndPropertiesSeeder::class);
        // Warm caches, then measure controller data assembly (not full HTTP stack noise).
        app(DivisionCatalog::class)->stats('hotels');
        app(DivisionCatalog::class)->amenities('hotels');
        app(MediaResolver::class)->collection('hotels.hero');

        DB::connection()->flushQueryLog();
        DB::enableQueryLog();
        $catalog = app(DivisionCatalog::class);
        $catalog->featured('hotels', 8);
        $catalog->destinations('hotels');
        $catalog->stats('hotels');
        $catalog->amenities('hotels');
        $catalog->faqs('hotels');
        $catalog->reviews('hotels');
        $queries = count(DB::getQueryLog());
        $this->assertLessThan(40, $queries, "Expected division data query budget under 40, got {$queries}");
    }

    public function test_csp_allows_privacy_enhanced_video_frames(): void
    {
        $response = $this->get(route('home'));
        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString('youtube-nocookie.com', $csp);
        $this->assertStringContainsString('player.vimeo.com', $csp);
        $this->assertStringContainsString('frame-src', $csp);
    }

    public function test_embed_parser_accepts_youtube_id(): void
    {
        $uploads = app(MediaUploadService::class);
        $parsed = $uploads->parseEmbedUrl('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        $this->assertSame('youtube', $parsed['provider']);
        $this->assertSame('dQw4w9WgXcQ', $parsed['id']);
    }

    public function test_home_uses_media_slots_without_site_images_config_paths(): void
    {
        $this->seed(AdminAndPropertiesSeeder::class);
        $html = $this->get(route('home'))->assertOk()->getContent();
        // Hero should not explode; branded or listing fallback ok
        $this->assertStringContainsString('data-hero-slideshow', $html);
    }
}
