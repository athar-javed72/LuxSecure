<?php

namespace Tests\Feature;

use App\Models\Listing;
use Database\Seeders\AdminAndPropertiesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class ProductionAlpineCspTest extends TestCase
{
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();

        if (File::exists(public_path('hot'))) {
            File::delete(public_path('hot'));
        }
    }

    public function test_production_pages_have_nonce_scripts_and_no_inline_x_data_object_literals(): void
    {
        $this->assertFileExists(public_path('build/manifest.json'));

        // Seed while still in testing — migrate/seed must not run under production.
        $this->seedRbac();
        $this->seed(AdminAndPropertiesSeeder::class);

        $admin = $this->makeAdmin();
        $client = $this->makeClient();
        $listing = Listing::query()->published()->firstOrFail();

        $this->app['env'] = 'production';
        config(['app.debug' => false]);

        $pages = [
            ['guest', route('home')],
            ['guest', route('properties')],
            ['guest', route('properties.show', $listing)],
            ['guest', route('login.form')],
            ['guest', route('register.form')],
            ['guest', route('contact')],
            ['client', route('profile')],
            ['client', route('bookings.mine')],
            ['admin', route('admin.dashboard')],
            ['admin', route('admin.listings.create')],
            ['admin', route('admin.roles.index')],
            ['admin', route('admin.users.create')],
        ];

        foreach ($pages as [$actor, $url]) {
            $response = match ($actor) {
                'admin' => $this->actingAs($admin)->get($url),
                'client' => $this->actingAs($client)->get($url),
                default => $this->get($url),
            };

            $response->assertOk("Expected 200 for {$url}");
            $html = $response->getContent();

            $this->assertNoInlineXDataObjectLiterals($html, $url);
            $this->assertScriptTagsHaveNonce($html, $url);

            $csp = (string) $response->headers->get('Content-Security-Policy');
            $this->assertNotSame('', $csp, "Missing CSP for {$url}");
            $this->assertStringNotContainsString('unsafe-eval', $csp, "CSP unsafe-eval on {$url}");
            $this->assertMatchesRegularExpression("/script-src[^;]*'nonce-/", $csp, "CSP nonce missing on {$url}");
        }
    }

    private function assertNoInlineXDataObjectLiterals(string $html, string $url): void
    {
        preg_match_all('/\bx-data\s*=\s*(["\'])(.*?)\1/s', $html, $matches);

        foreach ($matches[2] as $expression) {
            $this->assertStringNotContainsString(
                '{',
                $expression,
                "Inline object literal in x-data on {$url}: {$expression}"
            );
        }
    }

    private function assertScriptTagsHaveNonce(string $html, string $url): void
    {
        preg_match_all('/<script\b([^>]*)>/i', $html, $matches);

        $this->assertNotEmpty($matches[0], "Expected at least one script tag on {$url}");

        foreach ($matches[1] as $attrs) {
            $this->assertMatchesRegularExpression(
                '/\bnonce\s*=/',
                $attrs,
                "Script tag missing nonce on {$url}: <script{$attrs}>"
            );
        }
    }
}
