<?php

namespace Tests\Feature;

use App\Models\CancellationPolicy;
use App\Models\City;
use App\Models\Country;
use App\Models\CountryPaymentMethod;
use App\Models\CountrySetting;
use App\Models\Currency;
use App\Models\KycDocumentType;
use App\Models\Language;
use App\Models\Listing;
use App\Models\Organization;
use App\Models\Setting;
use App\Support\CountryContext;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\Support\CreatesListings;
use Tests\TestCase;

class MaskanoorReferenceDataTest extends TestCase
{
    use CreatesListings;
    use RefreshDatabase;

    public function test_reference_seeder_is_idempotent_and_does_not_overwrite_admin_edits(): void
    {
        $this->seed(ReferenceDataSeeder::class);

        Setting::setValue('site.name', 'Edited Brand', 'site', 'string', 'Site name');
        Country::query()->where('iso2', 'PK')->update(['tax_percent' => 7.5]);

        $this->seed(ReferenceDataSeeder::class);

        $this->assertSame('Edited Brand', Setting::getValue('site.name'));
        $this->assertEqualsWithDelta(7.5, (float) Country::query()->where('iso2', 'PK')->value('tax_percent'), 0.0001);
    }

    public function test_brand_and_multi_country_reference_data_seeded(): void
    {
        $this->seed(ReferenceDataSeeder::class);

        $this->assertSame('Maskanoor', Setting::getValue('site.name'));
        $this->assertSame('Elevating Spaces. Enriching Stays.', Setting::getValue('site.tagline.en'));
        $this->assertSame('/brand/logos/maskanoor-group-horizontal-for-light-bg.svg', Setting::getValue('site.logo'));
        $this->assertFileExists(public_path('brand/logos/maskanoor-group-horizontal-for-dark-bg.svg'));
        $this->assertFileExists(public_path('brand/logos/maskanoor-group-primary-for-dark-bg.svg'));
        $this->assertFileExists(public_path('brand/monogram/maskanoor-monogram-nostar-two-tone-for-dark-bg.svg'));
        $this->assertFileExists(public_path('brand/favicon/favicon.svg'));
        $this->assertFileExists(public_path('favicon.svg'));

        foreach (['PK', 'SA', 'AE', 'QA', 'MY', 'GB'] as $iso) {
            $this->assertDatabaseHas('countries', ['iso2' => $iso, 'is_active' => true]);
        }

        foreach (['PKR', 'SAR', 'AED', 'QAR', 'MYR', 'GBP'] as $code) {
            $this->assertTrue(Currency::query()->where('code', $code)->where('is_active', true)->exists());
        }

        foreach (['en', 'ur', 'ar', 'ms'] as $code) {
            $this->assertTrue(Language::query()->where('code', $code)->exists());
        }

        $this->assertTrue(Language::query()->where('code', 'ur')->where('is_rtl', true)->exists());
        $this->assertTrue(Language::query()->where('code', 'ar')->where('is_rtl', true)->where('needs_native_review', true)->exists());
        $this->assertTrue(Language::query()->where('code', 'ms')->where('needs_native_review', true)->exists());

        $this->assertTrue(Country::query()->where('iso2', 'PK')->where('tax_needs_review', true)->exists());
        $this->assertGreaterThanOrEqual(3, City::query()->whereHas('country', fn ($q) => $q->where('iso2', 'PK'))->count());
    }

    public function test_pakistan_contact_is_global_fallback_and_other_countries_have_no_fake_phones(): void
    {
        $this->seed(ReferenceDataSeeder::class);

        $this->assertSame('pmobie264@gmail.com', Setting::getValue('site.email'));
        $this->assertSame('+923228439536', Setting::getValue('site.phone'));

        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $this->assertSame('pmobie264@gmail.com', CountrySetting::getValue($pk->id, 'site.email'));

        $ae = Country::query()->where('iso2', 'AE')->firstOrFail();
        $this->assertNull(CountrySetting::getValue($ae->id, 'site.phone'));
        $this->assertNull(CountrySetting::getValue($ae->id, 'site.email'));

        session(['country_id' => $ae->id]);
        $this->assertSame('pmobie264@gmail.com', CountryContext::contact('site.email'));
        $this->assertSame('+923228439536', CountryContext::contact('site.phone'));
    }

    public function test_kyc_and_payment_methods_per_country(): void
    {
        $this->seed(ReferenceDataSeeder::class);

        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $this->assertTrue(KycDocumentType::query()->where('country_id', $pk->id)->where('code', 'cnic')->exists());
        $this->assertTrue(CountryPaymentMethod::query()->where('country_id', $pk->id)->where('gateway', 'bank_transfer')->where('is_enabled', true)->exists());
        $this->assertTrue(CountryPaymentMethod::query()->where('country_id', $pk->id)->where('gateway', 'jazzcash')->where('is_enabled', false)->exists());

        $ae = Country::query()->where('iso2', 'AE')->firstOrFail();
        $this->assertTrue(KycDocumentType::query()->where('country_id', $ae->id)->where('code', 'emirates_id')->exists());
        $this->assertFalse(CountryPaymentMethod::query()->where('country_id', $ae->id)->where('gateway', 'jazzcash')->exists());
        $this->assertTrue(CountryPaymentMethod::query()->where('country_id', $ae->id)->where('gateway', 'card')->where('is_enabled', false)->exists());
    }

    public function test_cancellation_policies_resolve_by_listing_purpose(): void
    {
        $this->seed(ReferenceDataSeeder::class);

        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();

        $hotel = $this->makeListing([
            'country_id' => $pk->id,
            'purpose' => 'short_stay',
            'cancellation_policy_id' => null,
            'organization_id' => null,
        ]);
        $rent = $this->makeListing([
            'country_id' => $pk->id,
            'purpose' => 'rent',
            'cancellation_policy_id' => null,
            'organization_id' => null,
            'title' => 'Rent Listing Policy Test',
        ]);
        $sale = $this->makeListing([
            'country_id' => $pk->id,
            'purpose' => 'sale',
            'cancellation_policy_id' => null,
            'organization_id' => null,
            'title' => 'Sale Listing Policy Test',
        ]);

        $this->assertSame('hotel-flexible', CancellationPolicy::resolveForListing($hotel)?->slug);
        $this->assertSame('rent-standard', CancellationPolicy::resolveForListing($rent)?->slug);
        $this->assertSame('sale-token', CancellationPolicy::resolveForListing($sale)?->slug);

        $hotelPolicy = CancellationPolicy::query()->where('slug', 'hotel-flexible')->firstOrFail();
        $this->assertSame('hotel', $hotelPolicy->applies_to);
        $this->assertSame('check_in', $hotelPolicy->reference_date);
        $this->assertGreaterThanOrEqual(3, $hotelPolicy->rules()->count());
    }

    public function test_rtl_shared_for_urdu_and_arabic(): void
    {
        $this->seed(ReferenceDataSeeder::class);
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();

        $this->withSession(['country_id' => $pk->id, 'locale' => 'ur'])
            ->get('/')
            ->assertOk()
            ->assertSee('dir="rtl"', false);

        $this->withSession(['country_id' => $pk->id, 'locale' => 'ar'])
            ->get('/')
            ->assertOk()
            ->assertSee('dir="rtl"', false);

        $this->withSession(['country_id' => $pk->id, 'locale' => 'en'])
            ->get('/')
            ->assertOk()
            ->assertDontSee('dir="rtl"', false);
    }

    public function test_production_seeder_creates_reference_but_no_demo_org(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('db:seed', [
            '--class' => DatabaseSeeder::class,
            '--force' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('countries', ['iso2' => 'PK']);
        $this->assertSame('Maskanoor', Setting::getValue('site.name'));
        $this->assertFalse(Organization::query()->where('slug', 'maskanoor-stays')->exists());
        $this->assertSame(0, Listing::query()->where('address', 'like', '%[DEMO]%')->count());
    }

    public function test_local_seeder_creates_maskanoor_stays_demo_branches(): void
    {
        $this->app['env'] = 'testing';
        $this->seed(DatabaseSeeder::class);

        $org = Organization::query()->where('slug', 'maskanoor-stays')->first();
        $this->assertNotNull($org);
        $this->assertSame('Maskanoor Stays', $org->name);

        $branches = Listing::query()->where('organization_id', $org->id)->get();
        $this->assertCount(3, $branches);
        foreach ($branches as $branch) {
            $this->assertStringContainsString('[DEMO]', (string) $branch->address);
            $this->assertSame('14:00:00', (string) $branch->check_in_time);
            $this->assertSame('12:00:00', (string) $branch->check_out_time);
        }
    }

    public function test_no_hardcoded_luxsecure_brand_in_runtime_sources(): void
    {
        $roots = [
            base_path('resources/views'),
            base_path('app'),
            base_path('config'),
            base_path('lang'),
            base_path('README.md'),
        ];

        $allowedFragments = [
            'Developed by Nexora Labs',
            'admin@luxsecure.com',
            'luxsecure:backup',
            'luxsecure:create-super-admin',
            'luxsecure:notify-rent-due',
            'luxsecure:expire-booking-holds',
            'config(\'luxsecure.',
            "config('luxsecure.",
            'config("luxsecure.',
        ];

        $hits = [];
        foreach ($roots as $root) {
            $paths = is_dir($root)
                ? File::allFiles($root)
                : [new \SplFileInfo($root)];

            foreach ($paths as $file) {
                $path = is_string($file) ? $file : $file->getPathname();
                if (str_contains($path, DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR)) {
                    continue;
                }
                if (str_ends_with($path, '.blade.php.php')) {
                    continue;
                }
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if (! in_array($ext, ['php', 'blade.php', 'md', 'css', 'js', 'json'], true) && ! str_ends_with($path, '.blade.php') && ! str_ends_with($path, 'README.md')) {
                    if (! str_ends_with($path, '.blade.php') && $ext !== 'md' && $ext !== 'php' && $ext !== 'css') {
                        continue;
                    }
                }

                $contents = File::get($path);
                if (! str_contains($contents, 'LuxSecure') && ! preg_match('/\bLS\b/', $contents)) {
                    continue;
                }

                $relative = str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
                if (str_contains($relative, 'scribe'.DIRECTORY_SEPARATOR) && str_contains($relative, 'endpoints')) {
                    continue;
                }

                foreach (preg_split('/\R/', $contents) as $line) {
                    if (! str_contains($line, 'LuxSecure') && ! preg_match('/\bLS\b/', $line)) {
                        continue;
                    }
                    $allowed = false;
                    foreach ($allowedFragments as $frag) {
                        if (str_contains($line, $frag)) {
                            $allowed = true;
                            break;
                        }
                    }
                    // Ignore artisan command namespace / package path leftovers that are not brand UI.
                    if (str_contains($line, 'luxsecure.com') || str_contains($line, 'luxsecure:')) {
                        $allowed = true;
                    }
                    if (! $allowed) {
                        $hits[] = $relative.': '.$line;
                    }
                }
            }
        }

        $this->assertSame([], $hits, "Hardcoded LuxSecure/LS still present:\n".implode("\n", array_slice($hits, 0, 40)));
    }
}
