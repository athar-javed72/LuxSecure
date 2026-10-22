<?php

namespace Tests\Feature;

use App\Http\Requests\HotelsSearchRequest;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Listing;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\Hotel\BookingAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesListings;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class HotelsSearchTest extends TestCase
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

    public function test_valid_search_stays_on_hospitality(): void
    {
        $this->seedStay('Islamabad Stay', 'Islamabad', 2, 15000);

        $response = $this->get(route('divisions.hospitality', [
            'destination' => 'Islamabad',
            'check_in' => now()->addDays(7)->toDateString(),
            'check_out' => now()->addDays(9)->toDateString(),
            'guests' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('Available stays', false);
        $response->assertSee('Islamabad Stay', false);
        $response->assertSee('Islamabad ·', false);
        $response->assertDontSee('Maskanoor Properties', false);
    }

    public function test_empty_destination_searches_all_stays_in_hotels_context(): void
    {
        $this->seedStay('City A Stay', 'Islamabad', 2, 12000);
        $this->seedStay('City B Stay', 'Dubai', 2, 18000);

        $response = $this->get(route('divisions.hospitality', [
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('Showing all destinations', false);
        $response->assertSee('City A Stay', false);
        $response->assertSee('City B Stay', false);
    }

    public function test_invalid_dates_are_blocked_and_stay_on_hotels(): void
    {
        $checkIn = now()->addDays(5)->toDateString();

        $response = $this->from(route('divisions.hospitality'))
            ->get(route('divisions.hospitality', [
                'destination' => 'Islamabad',
                'check_in' => $checkIn,
                'check_out' => $checkIn,
                'guests' => 2,
            ]));

        $response->assertRedirect(route('divisions.hospitality'));
        $response->assertSessionHasErrors('check_out');
    }

    public function test_past_check_in_is_rejected(): void
    {
        $response = $this->from(route('divisions.hospitality'))
            ->get(route('divisions.hospitality', [
                'destination' => 'Islamabad',
                'check_in' => now()->subDays(2)->toDateString(),
                'check_out' => now()->addDays(2)->toDateString(),
                'guests' => 2,
            ]));

        $response->assertRedirect(route('divisions.hospitality'));
        $response->assertSessionHasErrors('check_in');
    }

    public function test_check_out_before_check_in_is_rejected(): void
    {
        $response = $this->from(route('divisions.hospitality'))
            ->get(route('divisions.hospitality', [
                'destination' => 'Islamabad',
                'check_in' => now()->addDays(10)->toDateString(),
                'check_out' => now()->addDays(8)->toDateString(),
                'guests' => 2,
            ]));

        $response->assertRedirect(route('divisions.hospitality'));
        $response->assertSessionHasErrors('check_out');
    }

    public function test_stay_longer_than_max_nights_is_rejected(): void
    {
        $checkIn = now()->addDays(3)->toDateString();
        $checkOut = now()->addDays(3 + HotelsSearchRequest::MAX_NIGHTS + 1)->toDateString();

        $response = $this->from(route('divisions.hospitality'))
            ->get(route('divisions.hospitality', [
                'destination' => 'Islamabad',
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests' => 2,
            ]));

        $response->assertRedirect(route('divisions.hospitality'));
        $response->assertSessionHasErrors('check_out');
    }

    public function test_empty_results_state_is_shown(): void
    {
        $this->seedStay('Only Islamabad', 'Islamabad', 2, 10000);

        $response = $this->get(route('divisions.hospitality', [
            'destination' => 'Nowhereville',
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(5)->toDateString(),
            'guests' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('No stays for these dates', false);
        $response->assertSee('Change dates', false);
        $response->assertSee('Enquire for groups', false);
    }

    public function test_unavailable_stay_is_excluded_for_chosen_dates(): void
    {
        $manager = $this->makeHotelManager();
        $guest = $this->makeClient(['email' => 'hotels-guest@example.com']);
        $listing = $this->makeListing([
            'title' => 'Booked Out Hotel',
            'location' => 'Islamabad',
            'purpose' => 'short_stay',
            'price_unit' => 'per_night',
            'price' => 9000,
            'status' => 'published',
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
        ]);

        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Standard',
            'capacity' => 2,
            'base_price' => 9000,
            'is_active' => true,
        ]);

        Room::create([
            'room_type_id' => $roomType->id,
            'listing_id' => $listing->id,
            'number' => '1',
            'status' => 'available',
        ]);

        $checkIn = now()->addDays(20)->toDateString();
        $checkOut = now()->addDays(22)->toDateString();

        app(BookingAvailabilityService::class)->createBooking($roomType, $checkIn, $checkOut, [
            'user_id' => $guest->id,
            'guests' => 1,
            'status' => 'confirmed',
        ]);

        $this->assertSame(1, Booking::count());

        $response = $this->get(route('divisions.hospitality', [
            'destination' => 'Islamabad',
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
        ]));

        $response->assertOk();
        // Inventory exists but dates are fully booked → omit (not an Enquire card).
        $response->assertDontSee('Booked Out Hotel', false);
        $response->assertDontSee('Enquire for dates — Booked Out Hotel', false);
        $response->assertSee('No stays for these dates', false);
    }

    public function test_stay_without_inventory_shows_as_enquire_card(): void
    {
        $manager = $this->makeHotelManager(['email' => 'hm-enquire@example.com']);
        $this->makeListing([
            'title' => 'QA Enquire Dubai Residence',
            'location' => 'Dubai',
            'purpose' => 'short_stay',
            'price_unit' => 'per_night',
            'price' => 15000,
            'status' => 'published',
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
        ]);

        $checkIn = now()->addDays(7)->toDateString();
        $checkOut = now()->addDays(9)->toDateString();

        $response = $this->get(route('divisions.hospitality', [
            'destination' => 'Dubai',
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('QA Enquire Dubai Residence', false);
        $response->assertSee('Enquire for dates', false);
        $response->assertSee('1 stay found', false);
        $response->assertDontSee('No stays for these dates', false);
        $response->assertSee('Enquire for dates — QA Enquire Dubai Residence', false);
        $html = $response->getContent();
        $this->assertMatchesRegularExpression(
            '/mh-results[\s\S]*contact\?type=hospitality[^"]*stay=QA(?:\+|%20)Enquire(?:\+|%20)Dubai(?:\+|%20)Residence/',
            $html
        );
    }

    public function test_destination_filter_still_works_for_enquire_and_bookable(): void
    {
        $this->seedStay('QA Bookable Islamabad Stay', 'Islamabad', 2, 12000);

        $manager = $this->makeHotelManager(['email' => 'hm-kl@example.com']);
        $this->makeListing([
            'title' => 'QA Enquire KL Residence',
            'location' => 'Kuala Lumpur',
            'purpose' => 'short_stay',
            'price_unit' => 'per_night',
            'price' => 15000,
            'status' => 'published',
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
        ]);

        $params = [
            'check_in' => now()->addDays(8)->toDateString(),
            'check_out' => now()->addDays(10)->toDateString(),
            'guests' => 2,
        ];

        $kl = $this->get(route('divisions.hospitality', $params + ['destination' => 'Kuala Lumpur']));
        $kl->assertOk();
        $kl->assertSee('QA Enquire KL Residence', false);
        $kl->assertSee('Enquire for dates — QA Enquire KL Residence', false);
        $kl->assertDontSee('QA Bookable Islamabad Stay', false);

        $isb = $this->get(route('divisions.hospitality', $params + ['destination' => 'Islamabad']));
        $isb->assertOk();
        $isb->assertSee('QA Bookable Islamabad Stay', false);
        $isb->assertDontSee('QA Enquire KL Residence', false);
        $isb->assertSee('/night', false);
    }

    public function test_bookable_range_shows_and_blocked_range_hides_inventory_stay(): void
    {
        [$listing, $roomType] = $this->seedStay('QA Availability Proof Hotel', 'Islamabad', 2, 15000);
        $guest = $this->makeClient(['email' => 'avail-proof@example.com']);

        $freeIn = now()->addDays(30)->toDateString();
        $freeOut = now()->addDays(32)->toDateString();
        $blockIn = now()->addDays(40)->toDateString();
        $blockOut = now()->addDays(42)->toDateString();

        $availability = app(BookingAvailabilityService::class);
        $this->assertTrue($availability->isAvailable($roomType, $freeIn, $freeOut));
        $this->assertSame(1, $availability->availableRoomCount($roomType, $freeIn, $freeOut));

        $availability->createBooking($roomType, $blockIn, $blockOut, [
            'user_id' => $guest->id,
            'guests' => 1,
            'status' => 'confirmed',
        ]);

        $this->assertFalse($availability->isAvailable($roomType, $blockIn, $blockOut));
        $this->assertSame(0, $availability->availableRoomCount($roomType, $blockIn, $blockOut));

        $free = $this->get(route('divisions.hospitality', [
            'destination' => 'Islamabad',
            'check_in' => $freeIn,
            'check_out' => $freeOut,
            'guests' => 2,
        ]));
        $free->assertOk();
        $free->assertSee('QA Availability Proof Hotel', false);
        $free->assertSee('1 stay found', false);
        $free->assertDontSee('Enquire for dates — QA Availability Proof Hotel', false);

        $blocked = $this->get(route('divisions.hospitality', [
            'destination' => 'Islamabad',
            'check_in' => $blockIn,
            'check_out' => $blockOut,
            'guests' => 2,
        ]));
        $blocked->assertOk();
        $blocked->assertDontSee('QA Availability Proof Hotel', false);
        $blocked->assertSee('0 stays found', false);
        $blocked->assertSee('No stays for these dates', false);
    }

    public function test_showing_all_destinations_count_includes_enquire_stays(): void
    {
        $this->seedStay('Bookable Stay', 'Islamabad', 2, 11000);

        $manager = $this->makeHotelManager(['email' => 'hm-all@example.com']);
        $this->makeListing([
            'title' => 'Enquire Only Stay',
            'location' => 'Dubai',
            'purpose' => 'short_stay',
            'price_unit' => 'per_night',
            'price' => 9000,
            'status' => 'published',
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
        ]);

        $response = $this->get(route('divisions.hospitality', [
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guests' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('Showing all destinations', false);
        $response->assertSee('2 stays found', false);
        $response->assertSee('Bookable Stay', false);
        $response->assertSee('Enquire Only Stay', false);
        $response->assertSee('Enquire for dates', false);
    }

    public function test_enquire_stay_appears_even_when_country_is_inactive(): void
    {
        $country = Country::query()->firstOrCreate(
            ['iso2' => 'AE'],
            [
                'name' => 'United Arab Emirates',
                'currency_code' => 'AED',
                'default_locale' => 'en',
                'timezone' => 'Asia/Dubai',
                'phone_code' => '+971',
                'area_units' => ['sq_ft'],
                'is_active' => false,
                'wind_down' => false,
                'sort_order' => 99,
            ]
        );
        $country->forceFill(['is_active' => false, 'wind_down' => false])->save();

        $manager = $this->makeHotelManager(['email' => 'hm-ae@example.com']);
        $this->makeListing([
            'title' => 'QA Inactive Country Dubai Stay',
            'location' => 'Dubai Marina',
            'purpose' => 'short_stay',
            'price_unit' => 'per_night',
            'price' => 15000,
            'status' => 'published',
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
            'country_id' => $country->id,
        ]);

        $response = $this->get(route('divisions.hospitality', [
            'destination' => 'Dubai',
            'check_in' => now()->addDays(6)->toDateString(),
            'check_out' => now()->addDays(8)->toDateString(),
            'guests' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('QA Inactive Country Dubai Stay', false);
        $response->assertSee('Enquire for dates — QA Inactive Country Dubai Stay', false);
        $response->assertDontSee('No stays for these dates', false);
    }

    public function test_query_string_prefills_booking_bar(): void
    {
        $checkIn = now()->addDays(4)->toDateString();
        $checkOut = now()->addDays(6)->toDateString();

        $response = $this->get(route('divisions.hospitality', [
            'destination' => 'Dubai',
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 3,
        ]));

        $response->assertOk();
        $response->assertSee('value="Dubai"', false);
        $response->assertSee('value="'.$checkIn.'"', false);
        $response->assertSee('value="'.$checkOut.'"', false);
        $response->assertSee('value="3"', false);
        $response->assertSee('3 guests', false);
    }

    public function test_hotels_path_redirects_to_hospitality_preserving_query(): void
    {
        $response = $this->get('/hotels?destination=Islamabad&guests=2');

        $response->assertRedirect('/hospitality?destination=Islamabad&guests=2');
        $this->assertSame(301, $response->getStatusCode());
    }

    public function test_properties_search_still_goes_to_properties(): void
    {
        $this->seedStay('Prop Stay', 'Islamabad', 2, 11000);

        $response = $this->get(route('properties', [
            'purpose' => 'short_stay',
            'search' => 'Islamabad',
        ]));

        $response->assertOk();
        $response->assertSee('Properties', false);
        $response->assertSee('Prop Stay', false);
    }

    public function test_hotels_booking_links_do_not_point_at_properties_index(): void
    {
        $html = $this->get(route('divisions.hospitality'))->assertOk()->getContent();

        $this->assertStringNotContainsString('action="'.url('/properties').'"', $html);
        $this->assertStringContainsString('action="'.route('divisions.hospitality').'"', $html);
        $this->assertStringNotContainsString('/properties?purpose=short_stay', $html);
    }

    /**
     * @return array{0: Listing, 1: RoomType}
     */
    private function seedStay(string $title, string $location, int $capacity, float $price): array
    {
        $manager = $this->makeHotelManager(['email' => 'hm-'.md5($title).'@example.com']);
        $listing = $this->makeListing([
            'title' => $title,
            'location' => $location,
            'purpose' => 'short_stay',
            'price_unit' => 'per_night',
            'price' => $price,
            'status' => 'published',
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
        ]);

        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'Room',
            'capacity' => $capacity,
            'base_price' => $price,
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
