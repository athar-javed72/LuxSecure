<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\KycDocument;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\PhoneOtp;
use App\Models\Review;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\Tenancy;
use App\Notifications\KycResultNotification;
use App\Support\StorageDisk;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Support\CreatesListings;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class RemainingPlatformFeaturesTest extends TestCase
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
        Storage::fake(StorageDisk::uploads());
        Storage::fake(StorageDisk::private());
        config(['payments.jazzcash.integrity_salt' => 'test-salt']);
        config(['payments.easypaisa.secret' => 'ep-secret']);
    }

    public function test_footer_reads_site_settings(): void
    {
        Setting::setValue('site.name', 'Noir Estate', 'site');
        Setting::setValue('site.email', 'hello@noir.test', 'site');
        Setting::setValue('site.phone', '+92 311 1111111', 'site');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Noir Estate')
            ->assertSee('hello@noir.test')
            ->assertSee('+92 311 1111111');
    }

    public function test_bank_transfer_payment_and_admin_approval_creates_invoice(): void
    {
        Notification::fake();
        $client = $this->makeClient();
        $listing = $this->makeListing(['status' => 'published', 'purpose' => 'short_stay']);
        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Deluxe',
            'capacity' => 2,
            'base_price' => 5000,
            'is_active' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDays(2)->toDateString(),
            'check_out' => now()->addDays(4)->toDateString(),
            'guests' => 2,
            'total' => 10000,
            'status' => 'pending',
            'cancellation_policy' => 'flexible',
        ]);

        $proof = UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf');

        $this->actingAs($client)
            ->post(route('payments.initiate'), [
                'payable_type' => 'booking',
                'payable_id' => $booking->id,
                'gateway' => 'bank_transfer',
                'bank_reference' => 'TRX-1',
                'proof' => $proof,
                'idempotency_key' => 'idem-1',
            ])
            ->assertRedirect();

        $payment = Payment::first();
        $this->assertNotNull($payment);
        $this->assertSame('awaiting_approval', $payment->status);

        $admin = $this->makeAdmin();
        $this->actingAs($admin)
            ->post(route('admin.payments.approve', $payment))
            ->assertRedirect();

        $payment->refresh();
        $this->assertSame('paid', $payment->status);
        $this->assertNotNull($payment->invoice);
        $this->assertNotNull($payment->invoice->pdf_path);

        $this->actingAs($admin)
            ->get(route('admin.payments.export'))
            ->assertOk();
    }

    public function test_jazzcash_webhook_is_signed_and_idempotent(): void
    {
        config([
            'payments.jazzcash.enabled' => true,
            'payments.jazzcash.integrity_salt' => 'test-salt',
        ]);

        $client = $this->makeClient();
        $listing = $this->makeListing(['status' => 'published']);
        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Std',
            'capacity' => 2,
            'base_price' => 2000,
            'is_active' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDay()->toDateString(),
            'check_out' => now()->addDays(2)->toDateString(),
            'guests' => 1,
            'total' => 2000,
            'status' => 'pending',
            'cancellation_policy' => 'flexible',
        ]);

        Payment::create([
            'payable_type' => Booking::class,
            'payable_id' => $booking->id,
            'user_id' => $client->id,
            'amount' => 2000,
            'currency' => 'PKR',
            'gateway' => 'jazzcash',
            'status' => 'pending',
            'external_id' => 'JC-TEST-1',
        ]);

        $payload = [
            'pp_TxnRefNo' => 'JC-TEST-1',
            'pp_ResponseCode' => '000',
            'pp_Amount' => '2000.00',
            'pp_Currency' => 'PKR',
            'timestamp' => now()->toIso8601String(),
        ];
        ksort($payload);
        $hashPayload = $payload;
        $payload['pp_SecureHash'] = strtoupper(hash_hmac('sha256', implode('&', $hashPayload), 'test-salt'));

        $this->postJson(route('webhooks.payments.jazzcash'), $payload)->assertOk();
        $payment = Payment::where('external_id', 'JC-TEST-1')->first();
        $this->assertSame('paid', $payment->status);
        $this->assertNotNull($payment->invoice);

        $this->postJson(route('webhooks.payments.jazzcash'), $payload)->assertOk();
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('payment_webhook_events', 1);
    }

    public function test_review_requires_completed_booking_and_admin_moderation(): void
    {
        $client = $this->makeClient();
        $listing = $this->makeListing(['status' => 'published']);

        $this->actingAs($client)
            ->post(route('reviews.store', $listing), ['rating' => 5, 'body' => 'Great'])
            ->assertSessionHasErrors('rating');

        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Suite',
            'capacity' => 2,
            'base_price' => 1000,
            'is_active' => true,
        ]);
        Booking::create([
            'user_id' => $client->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'check_in' => now()->subDays(5)->toDateString(),
            'check_out' => now()->subDays(2)->toDateString(),
            'guests' => 1,
            'total' => 1000,
            'status' => 'completed',
            'cancellation_policy' => 'flexible',
        ]);

        $this->actingAs($client)
            ->post(route('reviews.store', $listing), ['rating' => 5, 'body' => 'Great stay'])
            ->assertRedirect();

        $review = Review::first();
        $this->assertSame('pending', $review->status);

        $admin = $this->makeAdmin();
        $this->actingAs($admin)
            ->patch(route('admin.reviews.moderate', $review), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertSame(5.0, $listing->fresh()->averageRating());
    }

    public function test_kyc_approval_sets_verified_and_blocks_unverified_publish(): void
    {
        Notification::fake();
        $agent = $this->makeAgent();
        $file = UploadedFile::fake()->create('cnic.pdf', 50, 'application/pdf');

        $this->actingAs($agent)
            ->post(route('kyc.store'), ['type' => 'cnic', 'file' => $file])
            ->assertRedirect();

        $doc = KycDocument::first();
        $this->assertSame('pending', $doc->status);

        $listing = $this->makeListing([
            'agent_id' => $agent->id,
            'status' => 'published',
            'title' => 'Should Stay Pending',
        ]);
        // Direct create bypasses controller; exercise controller path:
        $admin = $this->makeAdmin();
        $this->actingAs($agent)
            ->post(route('admin.listings.store'), [
                'listing_type_id' => $listing->listing_type_id,
                'title' => 'Unverified Publish Attempt',
                'location' => 'Karachi',
                'purpose' => 'sale',
                'price' => 1000000,
                'price_unit' => 'total',
                'status' => 'published',
                'currency' => 'PKR',
                'attributes' => ['bedrooms' => 3, 'bathrooms' => 2],
            ])
            ->assertRedirect();

        $created = Listing::query()->where('title', 'Unverified Publish Attempt')->first();
        $this->assertSame('pending', $created->status);

        $this->actingAs($admin)
            ->patch(route('admin.kyc.review', $doc), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertTrue($agent->fresh()->isKycVerified());
        Notification::assertSentTo($agent, KycResultNotification::class);
    }

    public function test_phone_otp_send_and_verify(): void
    {
        $user = $this->makeClient(['phone' => null]);

        $this->actingAs($user)
            ->post(route('phone.verify.send'), ['phone' => '+923001112233'])
            ->assertRedirect();

        $otp = PhoneOtp::latest()->first();
        $this->assertNotNull($otp);

        // Recover code by creating a known hash path: re-send is hashed; force known code for assert.
        $otp->update(['code_hash' => Hash::make('123456')]);

        $this->actingAs($user)
            ->post(route('phone.verify.confirm'), [
                'phone' => '+923001112233',
                'code' => '123456',
            ])
            ->assertRedirect(route('profile'));

        $this->assertTrue($user->fresh()->isPhoneVerified());
        $this->assertSame('+923001112233', $user->fresh()->phone);
    }

    public function test_notifications_bell_unread_count(): void
    {
        $user = $this->makeClient();
        $user->notify(new KycResultNotification(KycDocument::create([
            'user_id' => $user->id,
            'type' => 'cnic',
            'file_path' => 'kyc/x.pdf',
            'status' => 'approved',
            'reviewed_at' => now(),
        ])));

        $this->actingAs($user)
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertJson(['count' => 1]);
    }

    public function test_client_can_review_via_tenancy(): void
    {
        $client = $this->makeClient();
        $landlord = $this->makeLandlord();
        $listing = $this->makeListing(['status' => 'published', 'owner_id' => $landlord->id]);

        Tenancy::create([
            'listing_id' => $listing->id,
            'tenant_id' => $client->id,
            'landlord_id' => $landlord->id,
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'monthly_rent' => 50000,
            'security_deposit' => 50000,
            'status' => 'active',
        ]);

        $this->actingAs($client)
            ->post(route('reviews.store', $listing), ['rating' => 4, 'body' => 'Solid home'])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'listing_id' => $listing->id,
            'user_id' => $client->id,
            'rating' => 4,
        ]);
    }
}
