<?php

namespace Tests\Feature;

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TrustProxies;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\Hotel\BookingAvailabilityService;
use App\Services\ListingCacheService;
use App\Services\Messaging\LogMessageChannel;
use App\Services\Payments\Concerns\RecordsWebhookEvents;
use App\Services\Payments\PaymentLedgerService;
use App\Services\Payments\RefundCalculator;
use App\Services\SanctumAbilityResolver;
use App\Services\TwoFactorService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Vite;
use RuntimeException;
use Tests\Support\CreatesListings;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class LedgerHardeningTest extends TestCase
{
    use CreatesListings;
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRbac();
        $this->seed(SettingsSeeder::class);
        Mail::fake();
        config([
            'payments.jazzcash.enabled' => false,
            'payments.easypaisa.enabled' => false,
            'maskanoor.booking_hold_minutes' => 30,
            'maskanoor.trusted_proxies' => '',
            'maskanoor.messaging.default' => 'log',
            'maskanoor.messaging.allow_log_in_production' => false,
        ]);
    }

    public function test_backfill_booking_payments_is_idempotent(): void
    {
        $client = $this->makeClient();
        [$listing, $roomType] = $this->makeHotelFixture();
        $booking = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDays(2)->toDateString(),
            'check_out' => now()->addDays(4)->toDateString(),
            'guests' => 1,
            'total' => 5000,
            'status' => 'pending',
            'cancellation_policy' => 'flexible',
        ]);

        $legacyId = DB::table('booking_payments')->insertGetId([
            'booking_id' => $booking->id,
            'amount' => 5000,
            'method' => 'bank_transfer',
            'status' => 'pending',
            'reference' => 'LEGACY-1',
            'paid_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration = require database_path('migrations/2026_10_17_140512_backfill_booking_payments_into_payments_ledger.php');
        $migration->up();
        $this->assertSame(1, Payment::query()->where('idempotency_key', 'legacy-bp-'.$legacyId)->count());

        $migration->up();
        $this->assertSame(1, Payment::query()->where('idempotency_key', 'legacy-bp-'.$legacyId)->count());
        $this->assertSame(1, Payment::query()->count());
    }

    public function test_booking_create_does_not_write_booking_payments(): void
    {
        $guest = $this->makeClient();
        [$listing, $roomType] = $this->makeHotelFixture();

        $response = $this->actingAs($guest)->post(route('bookings.store'), [
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(5)->toDateString(),
            'guests' => 1,
            'cancellation_policy' => 'flexible',
        ]);

        $booking = Booking::query()->first();
        $response->assertRedirect(route('bookings.show', $booking));

        $this->assertDatabaseCount('booking_payments', 0);
        $this->assertDatabaseCount('payments', 1);
        $this->assertNotNull($booking->hold_expires_at);
    }

    public function test_cancel_and_refund_go_through_ledger_service(): void
    {
        $client = $this->makeClient();
        [$listing, $roomType] = $this->makeHotelFixture();
        $booking = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 1,
            'total' => 8000,
            'status' => 'confirmed',
            'cancellation_policy' => 'flexible',
        ]);
        $paid = Payment::create([
            'payable_type' => Booking::class,
            'payable_id' => $booking->id,
            'user_id' => $client->id,
            'amount' => 8000,
            'currency' => 'PKR',
            'gateway' => 'bank_transfer',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $result = app(PaymentLedgerService::class)->cancelBooking($booking, $client);
        $this->assertSame('cancelled', $result['booking']->status);
        $this->assertSame('refund_pending', $paid->fresh()->status);
        $this->assertSame('full', $paid->fresh()->meta['cancel_refund']['action'] ?? null);
    }

    public function test_hold_expiry_command_cancels_and_voids_pending_payments(): void
    {
        $client = $this->makeClient();
        [$listing, $roomType] = $this->makeHotelFixture();
        $booking = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(5)->toDateString(),
            'guests' => 1,
            'total' => 4000,
            'status' => 'pending',
            'cancellation_policy' => 'flexible',
            'hold_expires_at' => now()->subMinute(),
        ]);
        $payment = Payment::create([
            'payable_type' => Booking::class,
            'payable_id' => $booking->id,
            'user_id' => $client->id,
            'amount' => 4000,
            'currency' => 'PKR',
            'gateway' => 'bank_transfer',
            'status' => 'pending',
        ]);

        Artisan::call('maskanoor:expire-booking-holds');

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertSame('failed', $payment->fresh()->status);
        $this->assertNull($booking->fresh()->hold_expires_at);
    }

    public function test_expired_hold_does_not_block_availability(): void
    {
        $client = $this->makeClient();
        [$listing, $roomType] = $this->makeHotelFixture();
        $checkIn = now()->addDays(3)->toDateString();
        $checkOut = now()->addDays(5)->toDateString();

        Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'room_id' => $roomType->rooms()->first()->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 1,
            'total' => 4000,
            'status' => 'pending',
            'cancellation_policy' => 'flexible',
            'hold_expires_at' => now()->subMinutes(5),
        ]);

        $available = app(BookingAvailabilityService::class)->isAvailable($roomType, $checkIn, $checkOut);
        $this->assertTrue($available);
    }

    public function test_trust_proxies_defaults_to_none(): void
    {
        config(['maskanoor.trusted_proxies' => '']);
        $middleware = new TrustProxies;
        $ref = new \ReflectionClass($middleware);
        $prop = $ref->getProperty('proxies');
        $prop->setAccessible(true);
        $this->assertSame([], $prop->getValue($middleware));
    }

    public function test_two_factor_and_contact_are_throttled(): void
    {
        $user = $this->makeAdmin(['email' => 'admin-2fa-throttle@example.com']);
        // makeAdmin already enables 2FA for panel roles

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.challenge'));

        for ($i = 0; $i < 6; $i++) {
            $this->post(route('two-factor.verify'), ['code' => '000000'])->assertStatus(302);
        }
        $this->post(route('two-factor.verify'), ['code' => '000000'])->assertStatus(429);

        for ($i = 0; $i < 6; $i++) {
            $this->post(route('contact.store'), [
                'name' => 'Test',
                'email' => 't@example.com',
                'message' => 'Hello from throttle test '.$i,
            ]);
        }
        $this->post(route('contact.store'), [
            'name' => 'Test',
            'email' => 't@example.com',
            'message' => 'One too many',
        ])->assertStatus(429);
    }

    public function test_production_csp_has_nonce_without_unsafe_eval_or_inline_scripts(): void
    {
        $this->app['env'] = 'production';

        $middleware = app(SecurityHeaders::class);
        $request = Request::create('https://example.test/', 'GET');
        $request->server->set('HTTPS', 'on');

        $response = $middleware->handle($request, function () {
            return response('ok', 200);
        });

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertNotSame('', $csp);

        preg_match('/script-src ([^;]+)/', $csp, $m);
        $scriptSrc = $m[1] ?? '';
        $this->assertStringNotContainsString('unsafe-eval', $scriptSrc);
        $this->assertStringNotContainsString("'unsafe-inline'", $scriptSrc);
        $this->assertMatchesRegularExpression("/'nonce-[A-Za-z0-9+\/=]+'/", $scriptSrc);
        $this->assertNotSame('', (string) Vite::cspNonce());
    }

    public function test_refund_calculator_full_partial_and_none(): void
    {
        $calc = app(RefundCalculator::class);
        $client = $this->makeClient();
        [$listing, $roomType] = $this->makeHotelFixture();

        $flexible = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(5)->toDateString(),
            'guests' => 1,
            'total' => 1000,
            'status' => 'confirmed',
            'cancellation_policy' => 'flexible',
        ]);
        $this->assertSame('full', $calc->forBooking($flexible, 1000)['action']);

        $moderate = $flexible->replicate();
        $moderate->cancellation_policy = 'moderate';
        $moderate->check_in = now()->addDays(2)->toDateString();
        $moderate->save();
        $partial = $calc->forBooking($moderate, 1000);
        $this->assertSame('partial', $partial['action']);
        $this->assertSame('500', $partial['refund_amount']);

        $strict = $flexible->replicate();
        $strict->cancellation_policy = 'strict';
        $strict->check_in = now()->addDays(2)->toDateString();
        $strict->save();
        $this->assertSame('none', $calc->forBooking($strict, 1000)['action']);
    }

    public function test_admin_refund_route_requires_payments_refund_permission(): void
    {
        $client = $this->makeClient();
        $support = $this->makeUserWithRole('support');
        [$listing, $roomType] = $this->makeHotelFixture();
        $booking = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(4)->toDateString(),
            'guests' => 1,
            'total' => 1000,
            'status' => 'confirmed',
            'cancellation_policy' => 'flexible',
        ]);
        $payment = Payment::create([
            'payable_type' => Booking::class,
            'payable_id' => $booking->id,
            'user_id' => $client->id,
            'amount' => 1000,
            'currency' => 'PKR',
            'gateway' => 'bank_transfer',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->actingAs($support)
            ->post(route('admin.payments.refund', $payment))
            ->assertForbidden();

        $admin = $this->makeAdmin();
        $this->actingAs($admin)
            ->post(route('admin.payments.refund', $payment), ['reason' => 'Customer goodwill'])
            ->assertRedirect();
        $this->assertSame('refunded', $payment->fresh()->status);
    }

    public function test_sanctum_ability_denial(): void
    {
        $client = $this->makeClient();
        $limited = $client->createToken('api', ['listings:read'])->plainTextToken;

        $this->withToken($limited)
            ->getJson('/api/v1/profile')
            ->assertForbidden()
            ->assertJsonPath('code', 'forbidden');
    }

    public function test_no_full_token_before_2fa_setup(): void
    {
        $support = $this->makeUserWithRole('support', ['email' => 'support-setup@example.com']);
        $support->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $issued = app(SanctumAbilityResolver::class)->issueToken($support->fresh(), 'api', app(TwoFactorService::class));
        $this->assertSame([SanctumAbilityResolver::ABILITY_2FA_SETUP], $issued->accessToken->abilities);
        $this->assertFalse($issued->accessToken->can('profile:read'));
        $this->assertFalse($issued->accessToken->can('bookings:*'));

        $this->withToken($issued->plainTextToken)
            ->getJson('/api/v1/profile')
            ->assertForbidden()
            ->assertJsonPath('code', 'two_factor_setup_required');

        $this->withToken($issued->plainTextToken)
            ->postJson('/api/v1/two-factor/setup')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_sms_log_driver_blocked_in_production(): void
    {
        $this->app['env'] = 'production';
        config([
            'maskanoor.messaging.default' => 'log',
            'maskanoor.messaging.allow_log_in_production' => false,
        ]);

        $this->expectException(RuntimeException::class);
        (new LogMessageChannel('sms'))->send('+923001234567', 'test otp');
    }

    public function test_webhook_unique_race_returns_existing_payment_noop(): void
    {
        $client = $this->makeClient();
        $payment = Payment::create([
            'payable_type' => Booking::class,
            'payable_id' => 1,
            'user_id' => $client->id,
            'amount' => 1000,
            'currency' => 'PKR',
            'gateway' => 'jazzcash',
            'status' => 'pending',
            'external_id' => 'JC-RACE-1',
        ]);

        PaymentWebhookEvent::create([
            'gateway' => 'jazzcash',
            'event_id' => 'JC-RACE-1',
            'payload' => ['ok' => true],
            'status' => 'processed',
        ]);

        $helper = new class
        {
            use RecordsWebhookEvents;

            public function run(string $gateway, string $eventId, array $payload, Payment $payment): ?Payment
            {
                return $this->recordWebhookEventOrExisting($gateway, $eventId, $payload, $payment);
            }
        };

        $existing = $helper->run('jazzcash', 'JC-RACE-1', ['dup' => true], $payment);
        $this->assertNotNull($existing);
        $this->assertSame($payment->id, $existing->id);
        $this->assertSame(1, PaymentWebhookEvent::query()->count());
        $this->assertSame('pending', $payment->fresh()->status);
    }

    public function test_listing_cache_version_is_unique_across_flushes(): void
    {
        $cache = app(ListingCacheService::class);
        $store = Cache::store(config('maskanoor.listing_cache_store', 'array') === 'array'
            ? config('cache.default')
            : config('maskanoor.listing_cache_store', config('cache.default')));

        $cache->flush();
        $v1 = $store->get('listings.cache.version');
        usleep(1000);
        $cache->flush();
        $v2 = $store->get('listings.cache.version');

        $this->assertNotSame($v1, $v2);
        $this->assertDoesNotMatchRegularExpression('/^\d{10}$/', (string) $v1);
    }

    public function test_privileged_user_fields_are_not_mass_assignable(): void
    {
        $user = User::factory()->create();
        $user->update([
            'is_active' => false,
            'kyc_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
            'name' => 'Safe Name',
        ]);

        $user->refresh();
        $this->assertTrue($user->is_active);
        $this->assertNull($user->kyc_verified_at);
        $this->assertNull($user->two_factor_confirmed_at);
        $this->assertSame('Safe Name', $user->name);
    }

    public function test_soft_deleted_payments_excluded_from_export_query(): void
    {
        $client = $this->makeClient();
        $admin = $this->makeAdmin();
        $payment = Payment::create([
            'payable_type' => Booking::class,
            'payable_id' => 1,
            'user_id' => $client->id,
            'amount' => 100,
            'currency' => 'PKR',
            'gateway' => 'bank_transfer',
            'status' => 'paid',
            'paid_at' => now(),
        ]);
        $payment->delete();

        $this->actingAs($admin)
            ->get(route('admin.payments.export'))
            ->assertOk()
            ->assertDontSee($payment->uuid);
    }

    /**
     * @return array{0: Listing, 1: RoomType}
     */
    private function makeHotelFixture(): array
    {
        $listing = $this->makeListing(['status' => 'published', 'purpose' => 'short_stay']);
        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Standard',
            'capacity' => 2,
            'base_price' => 4000,
            'is_active' => true,
        ]);
        Room::create([
            'room_type_id' => $roomType->id,
            'listing_id' => $listing->id,
            'number' => '101',
            'status' => 'available',
        ]);

        return [$listing, $roomType];
    }
}
