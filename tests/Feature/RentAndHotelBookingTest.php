<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmationMail;
use App\Mail\TenancyCreatedMail;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SeasonalPrice;
use App\Models\Tenancy;
use App\Services\Hotel\BookingAvailabilityService;
use App\Services\Hotel\BookingPricingService;
use App\Services\Rent\RentScheduleGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Support\CreatesListings;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class RentAndHotelBookingTest extends TestCase
{
    use CreatesListings;
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRbac();
    }

    public function test_rent_schedule_generation_covers_each_month(): void
    {
        $landlord = $this->makeLandlord();
        $tenant = $this->makeClient(['email' => 'tenant@example.com']);
        $listing = $this->makeListing([
            'purpose' => 'rent',
            'price_unit' => 'per_month',
            'price' => 50000,
            'owner_id' => $landlord->id,
        ]);

        $tenancy = Tenancy::create([
            'listing_id' => $listing->id,
            'tenant_id' => $tenant->id,
            'landlord_id' => $landlord->id,
            'start_date' => '2026-01-15',
            'end_date' => '2026-04-14',
            'monthly_rent' => 50000,
            'security_deposit' => 100000,
            'status' => 'active',
        ]);

        $rows = app(RentScheduleGenerator::class)->generate($tenancy);

        $this->assertCount(3, $rows);
        $this->assertSame(['2026-01-15', '2026-02-15', '2026-03-15'], $rows->pluck('due_date')->all());
        $this->assertTrue($rows->every(fn ($r) => (float) $r['amount'] === 50000.0));
    }

    public function test_landlord_can_create_tenancy_and_schedule_is_persisted(): void
    {
        Mail::fake();

        $landlord = $this->makeLandlord();
        $tenant = $this->makeClient(['email' => 'tenant2@example.com']);
        $listing = $this->makeListing([
            'purpose' => 'rent',
            'owner_id' => $landlord->id,
            'price' => 40000,
            'price_unit' => 'per_month',
        ]);

        $response = $this->actingAs($landlord)->post(route('admin.tenancies.store'), [
            'listing_id' => $listing->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2026-03-01',
            'end_date' => '2026-05-31',
            'monthly_rent' => 40000,
            'security_deposit' => 80000,
        ]);

        $tenancy = Tenancy::first();
        $this->assertNotNull($tenancy);
        $response->assertRedirect(route('admin.tenancies.show', $tenancy));
        $this->assertCount(3, $tenancy->payments);
        Mail::assertQueued(TenancyCreatedMail::class);
    }

    public function test_landlord_cannot_view_another_landlords_tenancy(): void
    {
        $landlord = $this->makeLandlord();
        $other = $this->makeLandlord(['email' => 'other-landlord@example.com']);
        $tenant = $this->makeClient(['email' => 'tenant3@example.com']);
        $listing = $this->makeListing(['owner_id' => $other->id, 'purpose' => 'rent']);

        $tenancy = Tenancy::create([
            'listing_id' => $listing->id,
            'tenant_id' => $tenant->id,
            'landlord_id' => $other->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'monthly_rent' => 10000,
            'security_deposit' => 0,
            'status' => 'active',
        ]);

        $this->actingAs($landlord)->get(route('admin.tenancies.show', $tenancy))->assertForbidden();
    }

    public function test_client_sees_own_tenancy_and_payment_history(): void
    {
        $landlord = $this->makeLandlord();
        $tenant = $this->makeClient(['email' => 'tenant4@example.com']);
        $listing = $this->makeListing(['owner_id' => $landlord->id, 'purpose' => 'rent']);

        $tenancy = Tenancy::create([
            'listing_id' => $listing->id,
            'tenant_id' => $tenant->id,
            'landlord_id' => $landlord->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-01',
            'monthly_rent' => 25000,
            'security_deposit' => 0,
            'status' => 'active',
        ]);
        app(RentScheduleGenerator::class)->createFor($tenancy);

        $this->actingAs($tenant)
            ->get(route('tenancies.mine'))
            ->assertOk()
            ->assertSee($listing->title);

        $this->actingAs($tenant)
            ->get(route('tenancies.show', $tenancy))
            ->assertOk()
            ->assertSee('Payment history');
    }

    public function test_price_calculation_uses_seasonal_rates(): void
    {
        $manager = $this->makeHotelManager();
        $listing = $this->makeListing([
            'purpose' => 'short_stay',
            'price_unit' => 'per_night',
            'price' => 10000,
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
        ]);

        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Deluxe',
            'capacity' => 2,
            'base_price' => 10000,
            'amenities' => ['WiFi'],
            'is_active' => true,
        ]);

        SeasonalPrice::create([
            'room_type_id' => $roomType->id,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
            'price_per_night' => 15000,
        ]);

        $quote = app(BookingPricingService::class)->calculate($roomType->fresh('seasonalPrices'), '2026-05-31', '2026-06-03');

        // May 31 base, Jun 1 seasonal, Jun 2 seasonal
        $this->assertSame(3, $quote['nights']);
        $this->assertEquals(40000.0, $quote['total']);
    }

    public function test_overlapping_dates_are_detected_and_double_booking_is_blocked(): void
    {
        Mail::fake();

        $manager = $this->makeHotelManager();
        $guest = $this->makeClient(['email' => 'guest@example.com']);
        $guest2 = $this->makeClient(['email' => 'guest2@example.com']);

        $listing = $this->makeListing([
            'purpose' => 'short_stay',
            'price_unit' => 'per_night',
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
            'status' => 'published',
        ]);

        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Standard',
            'capacity' => 2,
            'base_price' => 8000,
            'is_active' => true,
        ]);

        Room::create([
            'room_type_id' => $roomType->id,
            'listing_id' => $listing->id,
            'number' => '101',
            'status' => 'available',
        ]);

        $availability = app(BookingAvailabilityService::class);

        $first = $availability->createBooking($roomType, '2026-07-10', '2026-07-12', [
            'user_id' => $guest->id,
            'guests' => 1,
            'status' => 'confirmed',
        ]);

        $this->assertInstanceOf(Booking::class, $first);
        $this->assertFalse($availability->isAvailable($roomType, '2026-07-11', '2026-07-13'));
        $this->assertTrue($availability->isAvailable($roomType, '2026-07-12', '2026-07-14'));

        $this->actingAs($guest2)->post(route('bookings.store'), [
            'room_type_id' => $roomType->id,
            'check_in' => '2026-07-11',
            'check_out' => '2026-07-13',
            'guests' => 1,
            'cancellation_policy' => 'flexible',
        ])->assertSessionHasErrors('check_in');

        $this->assertSame(1, Booking::count());
    }

    public function test_hotel_manager_only_sees_own_hotel_bookings(): void
    {
        $manager = $this->makeHotelManager();
        $other = $this->makeHotelManager(['email' => 'other-hm@example.com']);
        $guest = $this->makeClient(['email' => 'guest3@example.com']);

        $ownListing = $this->makeListing([
            'title' => 'Own Hotel',
            'purpose' => 'short_stay',
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
        ]);
        $otherListing = $this->makeListing([
            'title' => 'Other Hotel',
            'purpose' => 'short_stay',
            'agent_id' => $other->id,
            'owner_id' => $other->id,
        ]);

        $ownType = RoomType::create([
            'listing_id' => $ownListing->id,
            'name' => 'Suite',
            'capacity' => 2,
            'base_price' => 12000,
            'is_active' => true,
        ]);
        $otherType = RoomType::create([
            'listing_id' => $otherListing->id,
            'name' => 'Suite',
            'capacity' => 2,
            'base_price' => 12000,
            'is_active' => true,
        ]);

        Booking::create([
            'user_id' => $guest->id,
            'listing_id' => $ownListing->id,
            'room_type_id' => $ownType->id,
            'check_in' => '2026-08-01',
            'check_out' => '2026-08-03',
            'guests' => 1,
            'total' => 24000,
            'status' => 'confirmed',
            'cancellation_policy' => 'flexible',
        ]);

        Booking::create([
            'user_id' => $guest->id,
            'listing_id' => $otherListing->id,
            'room_type_id' => $otherType->id,
            'check_in' => '2026-08-05',
            'check_out' => '2026-08-07',
            'guests' => 1,
            'total' => 24000,
            'status' => 'confirmed',
            'cancellation_policy' => 'flexible',
        ]);

        $this->actingAs($manager)
            ->get(route('admin.bookings.index'))
            ->assertOk()
            ->assertSee('Own Hotel')
            ->assertDontSee('Other Hotel');
    }

    public function test_client_can_book_and_confirmation_is_queued(): void
    {
        Mail::fake();

        $manager = $this->makeHotelManager();
        $guest = $this->makeClient(['email' => 'guest4@example.com']);
        $listing = $this->makeListing([
            'purpose' => 'short_stay',
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
            'status' => 'published',
        ]);
        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'King',
            'capacity' => 2,
            'base_price' => 9000,
            'is_active' => true,
        ]);
        Room::create([
            'room_type_id' => $roomType->id,
            'listing_id' => $listing->id,
            'number' => '201',
            'status' => 'available',
        ]);

        $this->actingAs($guest)->post(route('bookings.store'), [
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(5)->toDateString(),
            'guests' => 2,
            'cancellation_policy' => 'moderate',
        ])->assertRedirect(route('bookings.mine'));

        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('booking_payments', 0);
        Mail::assertQueued(BookingConfirmationMail::class);

        $this->actingAs($guest)
            ->get(route('bookings.mine'))
            ->assertOk()
            ->assertSee($listing->title);
    }

    public function test_agent_cannot_access_tenancy_admin(): void
    {
        $agent = $this->makeAgent();
        $this->actingAs($agent)->get(route('admin.tenancies.index'))->assertForbidden();
    }
}
