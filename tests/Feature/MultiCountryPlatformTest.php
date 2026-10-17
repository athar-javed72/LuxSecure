<?php

namespace Tests\Feature;

use App\Models\CancellationPolicy;
use App\Models\Country;
use App\Models\CountryPaymentMethod;
use App\Models\Currency;
use App\Models\KycDocumentType;
use App\Models\Listing;
use App\Models\Organization;
use App\Models\Page;
use App\Models\User;
use App\Services\Payments\RefundCalculator;
use App\Support\Money;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class MultiCountryPlatformTest extends TestCase
{
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_pakistan_and_currencies_are_backfilled(): void
    {
        $this->assertDatabaseHas('countries', ['iso2' => 'PK', 'currency_code' => 'PKR']);
        $this->assertDatabaseHas('currencies', ['code' => 'PKR']);
        $this->assertTrue(CountryPaymentMethod::query()->where('gateway', 'bank_transfer')->where('is_enabled', true)->exists());
        $this->assertTrue(CountryPaymentMethod::query()->where('gateway', 'jazzcash')->where('is_enabled', false)->exists());
        $this->assertGreaterThanOrEqual(3, KycDocumentType::query()->whereHas('country', fn ($q) => $q->where('iso2', 'PK'))->count());
    }

    public function test_hotel_listings_become_organization_branches(): void
    {
        $hotel = Listing::query()->where('slug', 'blue-area-boutique-hotel')->first();
        $this->assertNotNull($hotel);
        $this->assertNotNull($hotel->organization_id);
        $this->assertNotNull($hotel->country_id);
        $this->assertDatabaseHas('organizations', ['id' => $hotel->organization_id]);
    }

    public function test_refund_calculator_reads_db_rules(): void
    {
        $policy = CancellationPolicy::query()->where('slug', 'flexible')->whereNull('country_id')->first();
        $this->assertNotNull($policy);
        $this->assertGreaterThanOrEqual(2, $policy->rules()->count());

        $hotel = Listing::query()->where('slug', 'blue-area-boutique-hotel')->firstOrFail();
        $user = $this->makeClient();
        $booking = \App\Models\Booking::query()->create([
            'country_id' => $hotel->country_id,
            'user_id' => $user->id,
            'listing_id' => $hotel->id,
            'room_type_id' => $hotel->roomTypes()->first()->id,
            'room_id' => $hotel->rooms()->first()->id,
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(5)->toDateString(),
            'guests' => 1,
            'total' => 10000,
            'currency' => 'PKR',
            'status' => 'confirmed',
            'cancellation_policy' => 'flexible',
            'cancellation_policy_id' => $policy->id,
        ]);

        $result = app(RefundCalculator::class)->forBooking($booking, 10000);
        $this->assertSame(100.0, $result['refund_percent']);
        $this->assertSame('10000.00', $result['refund_amount']);
    }

    public function test_legal_page_fallback_country_then_global(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        Page::query()->where('type', 'privacy')->update(['status' => 'draft', 'is_published' => false]);

        Page::query()->updateOrCreate(
            ['slug' => 'privacy-global-pub', 'country_id' => null, 'locale' => 'en'],
            [
                'title' => 'Global Privacy',
                'type' => 'privacy',
                'body' => 'Global [PLACEHOLDER: BUSINESS_NAME]',
                'status' => 'published',
                'is_published' => true,
                'version' => '2.0',
            ]
        );

        $resolved = Page::resolveLegal('privacy', $pk->id, 'en');
        $this->assertNotNull($resolved);
        $this->assertNull($resolved->country_id);

        Page::query()->updateOrCreate(
            ['slug' => 'privacy-pk-pub', 'country_id' => $pk->id, 'locale' => 'en'],
            [
                'title' => 'PK Privacy',
                'type' => 'privacy',
                'body' => 'PK body',
                'status' => 'published',
                'is_published' => true,
                'version' => '2.0',
            ]
        );

        $resolvedPk = Page::resolveLegal('privacy', $pk->id, 'en');
        $this->assertSame($pk->id, $resolvedPk->country_id);
    }

    public function test_money_formats_without_hardcoded_pkr_prefix_requirement(): void
    {
        $formatted = Money::format(25000, 'PKR', 'en');
        $this->assertNotEmpty($formatted);
        $this->assertTrue(str_contains($formatted, '25') || str_contains($formatted, '25000'));
    }

    public function test_country_admin_is_scoped_to_country_listings(): void
    {
        $this->seedRbac();
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $ae = Country::query()->create([
            'iso2' => 'AE',
            'name' => 'United Arab Emirates',
            'currency_code' => 'AED',
            'default_locale' => 'en',
            'timezone' => 'Asia/Dubai',
            'phone_code' => '+971',
            'area_units' => ['sq_m'],
            'is_active' => true,
            'sort_order' => 2,
        ]);
        Currency::query()->firstOrCreate(['code' => 'AED'], ['symbol' => 'د.إ', 'decimals' => 2, 'is_active' => true]);

        $admin = User::factory()->create(['country_id' => $pk->id, 'email_verified_at' => now(), 'is_active' => true]);
        $admin->assignRole('country_admin');

        $pkListing = Listing::query()->where('country_id', $pk->id)->first();
        $this->assertNotNull($pkListing);
        $this->assertTrue($admin->can('update', $pkListing));

        $foreign = $pkListing->replicate();
        $foreign->slug = 'ae-branch-test';
        $foreign->country_id = $ae->id;
        $foreign->title = 'Dubai Branch';
        $foreign->save();

        $this->assertFalse($admin->can('update', $foreign->fresh()));
    }

    public function test_adding_country_via_admin_needs_no_code(): void
    {
        $super = $this->makeSuperAdmin();
        $this->actingAs($super)
            ->post(route('admin.countries.store'), [
                'iso2' => 'GB',
                'name' => 'United Kingdom',
                'currency_code' => 'PKR',
                'default_locale' => 'en',
                'timezone' => 'Europe/London',
                'phone_code' => '+44',
                'area_units' => ['sq_m'],
                'tax_percent' => 20,
                'is_active' => 1,
                'sort_order' => 9,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('countries', ['iso2' => 'GB', 'name' => 'United Kingdom']);
    }

    public function test_backfill_migration_is_idempotent(): void
    {
        Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_17_180000_create_multi_country_platform_tables.php', '--force' => true]);
        $count = Country::query()->where('iso2', 'PK')->count();
        Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_17_180000_create_multi_country_platform_tables.php', '--force' => true]);
        $this->assertSame($count, Country::query()->where('iso2', 'PK')->count());
    }

    public function test_no_hardcoded_pkr_display_left_in_booking_views(): void
    {
        $paths = [
            base_path('resources/views/bookings/show.blade.php'),
            base_path('resources/views/bookings/mine.blade.php'),
            base_path('resources/views/properties/show.blade.php'),
            base_path('app/Notifications/RentDueNotification.php'),
        ];

        foreach ($paths as $path) {
            $contents = file_get_contents($path);
            $this->assertStringNotContainsString(' PKR', $contents, basename($path).' still hardcodes PKR display');
            $this->assertStringNotContainsString('}} PKR', $contents, basename($path));
        }
    }

    public function test_organization_public_page_lists_branches(): void
    {
        $org = Organization::query()->first();
        $this->assertNotNull($org);
        $this->get(route('organizations.show', $org))->assertOk();
    }

    public function test_csp_nonce_still_present_on_home(): void
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);

        $response = $this->get('/');
        $response->assertOk();
        $csp = $response->headers->get('Content-Security-Policy') ?? '';
        $this->assertStringContainsString('nonce-', $csp);
        $this->assertStringNotContainsString('unsafe-eval', $csp);
    }

    public function test_published_legal_version_requires_reacceptance(): void
    {
        $user = $this->makeClient();
        $page = Page::query()->updateOrCreate(
            ['slug' => 'terms-pub-v1', 'country_id' => null, 'locale' => 'en'],
            [
                'title' => 'Terms',
                'type' => 'terms',
                'body' => 'v1 [PLACEHOLDER: BUSINESS_NAME]',
                'status' => 'published',
                'is_published' => true,
                'version' => '1.0',
            ]
        );

        \App\Models\LegalAcceptance::record($user, $page, '127.0.0.1');
        $this->assertFalse(\App\Support\LegalAcceptanceService::requiresAcceptance($user->fresh()));

        $page->update(['version' => '2.0']);
        $this->assertTrue(\App\Support\LegalAcceptanceService::requiresAcceptance($user->fresh()));

        $this->actingAs($user)
            ->get(route('bookings.mine'))
            ->assertRedirect(route('legal.accept'));
    }

    public function test_organization_report_is_available_to_super_admin(): void
    {
        $org = Organization::query()->firstOrFail();
        $this->actingAs($this->makeSuperAdmin())
            ->get(route('admin.organizations.report', $org))
            ->assertOk()
            ->assertSee('report', false);
    }
}
