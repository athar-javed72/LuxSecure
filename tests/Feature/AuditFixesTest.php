<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Models\PhoneOtp;
use App\Models\RoomType;
use App\Services\Payments\Drivers\JazzCashGateway;
use App\Services\PhoneOtpService;
use App\Support\StorageDisk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\Support\CreatesListings;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class AuditFixesTest extends TestCase
{
    use CreatesListings;
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRbac();
        $this->seed(\Database\Seeders\SettingsSeeder::class);
        Storage::fake(StorageDisk::uploads());
        Storage::fake(StorageDisk::private());
        config([
            'payments.jazzcash.enabled' => false,
            'payments.easypaisa.enabled' => false,
            'payments.jazzcash.integrity_salt' => 'test-salt',
            'payments.easypaisa.secret' => 'ep-secret',
            'sanctum.expiration' => 1440,
        ]);
    }

    public function test_inactive_web_session_is_blocked(): void
    {
        $user = $this->makeClient();
        $this->actingAs($user);
        $user->update(['is_active' => false]);

        $this->get(route('profile'))
            ->assertRedirect(route('login.form'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_api_token_is_blocked(): void
    {
        $user = $this->makeClient();
        $token = $user->createToken('api')->plainTextToken;
        $user->update(['is_active' => false]);

        $this->withToken($token)
            ->getJson('/api/v1/profile')
            ->assertForbidden()
            ->assertJsonPath('code', 'account_inactive');

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_webhook_with_wrong_amount_is_rejected(): void
    {
        config(['payments.jazzcash.enabled' => true]);
        [$payment] = $this->makeJazzCashPendingPayment(2000);

        $payload = $this->signedJazzCashPayload([
            'pp_TxnRefNo' => $payment->external_id,
            'pp_ResponseCode' => '000',
            'pp_Amount' => '1999.00',
            'pp_Currency' => 'PKR',
            'timestamp' => now()->toIso8601String(),
        ]);

        $this->postJson(route('webhooks.payments.jazzcash'), $payload)
            ->assertStatus(422);

        $this->assertSame('pending', $payment->fresh()->status);
        $this->assertDatabaseCount('payment_webhook_events', 0);
    }

    public function test_duplicate_webhook_is_a_noop(): void
    {
        config(['payments.jazzcash.enabled' => true]);
        [$payment] = $this->makeJazzCashPendingPayment(2000);

        $payload = $this->signedJazzCashPayload([
            'pp_TxnRefNo' => $payment->external_id,
            'pp_ResponseCode' => '000',
            'pp_Amount' => '2000.00',
            'pp_Currency' => 'PKR',
            'timestamp' => now()->toIso8601String(),
        ]);

        $this->postJson(route('webhooks.payments.jazzcash'), $payload)->assertOk();
        $this->postJson(route('webhooks.payments.jazzcash'), $payload)->assertOk();

        $this->assertSame('paid', $payment->fresh()->status);
        $this->assertDatabaseCount('payment_webhook_events', 1);
    }

    public function test_concurrent_webhook_processed_once(): void
    {
        config(['payments.jazzcash.enabled' => true]);
        [$payment] = $this->makeJazzCashPendingPayment(3500);
        $driver = app(JazzCashGateway::class);

        $payload = $this->signedJazzCashPayload([
            'pp_TxnRefNo' => $payment->external_id,
            'pp_ResponseCode' => '000',
            'pp_Amount' => '3500.00',
            'pp_Currency' => 'PKR',
            'timestamp' => now()->toIso8601String(),
        ]);

        $first = $driver->handleWebhook($payload);
        $second = $driver->handleWebhook($payload);

        $this->assertSame('paid', $first->status);
        $this->assertSame('paid', $second->status);
        $this->assertSame(1, PaymentWebhookEvent::query()->where('event_id', $payment->external_id)->count());
        $this->assertSame(1, Payment::query()->whereKey($payment->id)->where('status', 'paid')->count());
    }

    public function test_admin_cannot_grant_direct_permissions_or_assign_admin_roles(): void
    {
        $admin = $this->makeAdmin();
        $target = $this->makeClient(['email' => 'escalation@example.com']);

        $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'is_active' => 1,
            'roles' => ['client'],
            'permissions' => ['panel.access', 'users.create', 'listings.view.any'],
        ])->assertRedirect();

        $target->refresh();
        $this->assertCount(0, $target->getDirectPermissions());

        $this->actingAs($admin)->from(route('admin.users.edit', $target))->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'is_active' => 1,
            'roles' => ['admin'],
        ])->assertSessionHasErrors('roles');

        $this->assertFalse($target->fresh()->hasRole('admin'));

        $this->actingAs($admin)->from(route('admin.users.edit', $target))->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'is_active' => 1,
            'roles' => ['super_admin'],
        ])->assertSessionHasErrors('roles');

        $this->assertFalse($target->fresh()->hasRole('super_admin'));
    }

    public function test_bulk_publish_blocked_for_unverified_agent(): void
    {
        $agent = $this->makeAgent(['email' => 'unverified-agent@example.com']);
        $this->assertNull($agent->kyc_verified_at);

        $listing = $this->makeListing([
            'title' => 'Unverified Bulk',
            'agent_id' => $agent->id,
            'owner_id' => $agent->id,
            'status' => 'draft',
        ]);

        $this->actingAs($agent)->post(route('admin.listings.bulk'), [
            'ids' => [$listing->id],
            'action' => 'status',
            'status' => 'published',
        ])->assertRedirect();

        $this->assertSame('pending', $listing->fresh()->status);
    }

    public function test_disabled_gateways_reject_initiate_and_webhook(): void
    {
        $client = $this->makeClient();
        $listing = $this->makeListing(['status' => 'published', 'purpose' => 'short_stay']);
        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Std',
            'capacity' => 2,
            'base_price' => 1000,
            'is_active' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDay()->toDateString(),
            'check_out' => now()->addDays(2)->toDateString(),
            'guests' => 1,
            'total' => 1000,
            'status' => 'pending',
            'cancellation_policy' => 'flexible',
        ]);

        $this->actingAs($client)
            ->from(route('home'))
            ->post(route('payments.initiate'), [
                'payable_type' => 'booking',
                'payable_id' => $booking->id,
                'gateway' => 'jazzcash',
            ])
            ->assertSessionHasErrors('gateway');

        $this->postJson(route('webhooks.payments.jazzcash'), [
            'pp_TxnRefNo' => 'x',
            'timestamp' => now()->toIso8601String(),
        ])->assertStatus(422);

        $this->postJson(route('webhooks.payments.easypaisa'), [
            'orderId' => 'x',
            'timestamp' => now()->toIso8601String(),
        ])->assertStatus(422);
    }

    public function test_bank_approve_without_proof_fails_and_reapprove_paid_fails(): void
    {
        $client = $this->makeClient();
        $admin = $this->makeAdmin();
        $listing = $this->makeListing(['status' => 'published', 'purpose' => 'short_stay']);
        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Std',
            'capacity' => 2,
            'base_price' => 5000,
            'is_active' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDay()->toDateString(),
            'check_out' => now()->addDays(2)->toDateString(),
            'guests' => 1,
            'total' => 5000,
            'status' => 'pending',
            'cancellation_policy' => 'flexible',
        ]);

        $payment = Payment::create([
            'payable_type' => Booking::class,
            'payable_id' => $booking->id,
            'user_id' => $client->id,
            'amount' => 5000,
            'currency' => 'PKR',
            'gateway' => 'bank_transfer',
            'status' => 'awaiting_approval',
            'proof_path' => null,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.payments.show', $payment))
            ->post(route('admin.payments.approve', $payment))
            ->assertSessionHasErrors('proof');

        $this->assertSame('awaiting_approval', $payment->fresh()->status);

        $payment->update([
            'proof_path' => 'payment-proofs/ok.pdf',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->from(route('admin.payments.show', $payment))
            ->post(route('admin.payments.approve', $payment))
            ->assertSessionHasErrors('status');
    }

    public function test_refund_flow_and_status_machine(): void
    {
        Mail::fake();
        $client = $this->makeClient();
        $admin = $this->makeAdmin();
        $listing = $this->makeListing(['status' => 'published', 'purpose' => 'short_stay']);
        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Suite',
            'capacity' => 2,
            'base_price' => 4000,
            'is_active' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(5)->toDateString(),
            'guests' => 1,
            'total' => 8000,
            'status' => 'confirmed',
            'cancellation_policy' => 'flexible',
        ]);

        $pending = Payment::create([
            'payable_type' => Booking::class,
            'payable_id' => $booking->id,
            'user_id' => $client->id,
            'amount' => 1000,
            'currency' => 'PKR',
            'gateway' => 'bank_transfer',
            'status' => 'pending',
        ]);

        $paid = Payment::create([
            'payable_type' => Booking::class,
            'payable_id' => $booking->id,
            'user_id' => $client->id,
            'amount' => 7000,
            'currency' => 'PKR',
            'gateway' => 'bank_transfer',
            'status' => 'paid',
            'proof_path' => 'payment-proofs/paid.pdf',
            'paid_at' => now(),
        ]);

        $this->actingAs($client)
            ->post(route('bookings.cancel', $booking))
            ->assertRedirect();

        $this->assertSame('failed', $pending->fresh()->status);
        $this->assertSame('refund_pending', $paid->fresh()->status);

        $this->actingAs($admin)
            ->post(route('admin.payments.refund', $paid))
            ->assertRedirect();

        $this->assertSame('refunded', $paid->fresh()->status);
        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Payment::class,
            'subject_id' => $paid->id,
            'description' => 'payment_refunded',
        ]);
    }

    public function test_expired_sanctum_token_is_rejected(): void
    {
        $user = $this->makeClient();
        $plain = $user->createToken('api')->plainTextToken;

        /** @var PersonalAccessToken $token */
        $token = $user->tokens()->first();
        $token->forceFill(['created_at' => now()->subMinutes(1441)])->save();

        $this->withToken($plain)
            ->getJson('/api/v1/profile')
            ->assertUnauthorized();
    }

    public function test_two_factor_required_for_staff_roles(): void
    {
        $support = $this->makeUserWithRole('support', ['email' => 'support-2fa@example.com']);
        $support->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->actingAs($support)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.two-factor.show'));

        $token = $support->createToken('api')->plainTextToken;
        $this->withToken($token)
            ->getJson('/api/v1/profile')
            ->assertForbidden()
            ->assertJsonPath('code', 'two_factor_setup_required');
    }

    public function test_old_otp_invalidated_when_new_otp_sent(): void
    {
        $user = $this->makeClient(['phone' => null]);
        $phone = '+923009998877';

        $old = PhoneOtp::create([
            'user_id' => $user->id,
            'phone' => $phone,
            'code_hash' => Hash::make('111111'),
            'expires_at' => now()->addMinutes(10),
            'consumed_at' => null,
        ]);

        app(PhoneOtpService::class)->send($user, $phone);

        $this->assertNotNull($old->fresh()->consumed_at);
        $this->assertSame(1, PhoneOtp::query()
            ->where('user_id', $user->id)
            ->where('phone', $phone)
            ->whereNull('consumed_at')
            ->count());
    }

    public function test_private_document_idor_denied(): void
    {
        $owner = $this->makeLandlord(['email' => 'owner-doc@example.com']);
        $stranger = $this->makeAgent(['email' => 'stranger-doc@example.com']);

        StorageDisk::privateDisk()->put('listings/private/secret.pdf', 'confidential');

        $listing = $this->makeListing([
            'owner_id' => $owner->id,
            'agent_id' => $owner->id,
            'private_document_path' => 'listings/private/secret.pdf',
        ]);

        $this->actingAs($stranger)
            ->get(route('admin.listings.private-document', $listing))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('admin.listings.private-document', $listing))
            ->assertOk();
    }

    public function test_room_type_permission_boundary(): void
    {
        $agent = $this->makeAgent(['email' => 'agent-rooms@example.com']);
        $manager = $this->makeHotelManager(['email' => 'hm-rooms@example.com']);

        $this->actingAs($agent)
            ->get(route('admin.room-types.index'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->get(route('admin.room-types.index'))
            ->assertOk();
    }

    public function test_app_timezone_is_asia_karachi(): void
    {
        $this->assertSame('Asia/Karachi', config('app.timezone'));
    }

    /**
     * @return array{0: Payment, 1: Booking}
     */
    private function makeJazzCashPendingPayment(float $amount): array
    {
        $client = $this->makeClient();
        $listing = $this->makeListing(['status' => 'published', 'purpose' => 'short_stay']);
        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Std',
            'capacity' => 2,
            'base_price' => $amount,
            'is_active' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDay()->toDateString(),
            'check_out' => now()->addDays(2)->toDateString(),
            'guests' => 1,
            'total' => $amount,
            'status' => 'pending',
            'cancellation_policy' => 'flexible',
        ]);

        $payment = Payment::create([
            'payable_type' => Booking::class,
            'payable_id' => $booking->id,
            'user_id' => $client->id,
            'amount' => $amount,
            'currency' => 'PKR',
            'gateway' => 'jazzcash',
            'status' => 'pending',
            'external_id' => 'JC-AUDIT-'.uniqid(),
        ]);

        return [$payment, $booking];
    }

    /** @param  array<string, string>  $payload */
    private function signedJazzCashPayload(array $payload): array
    {
        ksort($payload);
        $hashPayload = $payload;
        $payload['pp_SecureHash'] = strtoupper(hash_hmac('sha256', implode('&', $hashPayload), 'test-salt'));

        return $payload;
    }
}
