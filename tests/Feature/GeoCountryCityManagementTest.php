<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\City;
use App\Models\Country;
use App\Models\Listing;
use App\Models\ListingType;
use App\Support\CountryCatalog;
use App\Support\CountryReadiness;
use App\Support\CountryVisibility;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Laravel\Sanctum\Sanctum;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class GeoCountryCityManagementTest extends TestCase
{
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        CountryCatalog::flush();
    }

    private function fixtureGeoPath(): string
    {
        $dest = storage_path('app/geo-test');
        File::ensureDirectoryExists($dest);
        foreach (['countries.json', 'states.json', 'cities.json'] as $file) {
            File::copy(base_path('tests/fixtures/geo/'.$file), $dest.DIRECTORY_SEPARATOR.$file);
        }

        return $dest;
    }

    public function test_geo_import_is_idempotent_and_preserves_city_ids(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $lahore = City::query()->where('country_id', $pk->id)->where('name', 'Lahore')->firstOrFail();
        $lahoreId = $lahore->id;

        $source = $this->fixtureGeoPath();
        Artisan::call('maskanoor:geo:import', ['--source' => $source, '--country' => 'PK,AE,TL']);
        Artisan::call('maskanoor:geo:import', ['--source' => $source, '--country' => 'PK,AE,TL']);

        $this->assertSame($lahoreId, City::query()->where('name', 'Lahore')->where('country_id', $pk->id)->value('id'));
        $this->assertNotNull(City::query()->where('source_id', 10001)->first());
        $this->assertSame(1, City::query()->where('source_id', 10001)->count());

        $tl = Country::query()->where('iso2', 'TL')->first();
        $this->assertNotNull($tl);
        $this->assertFalse($tl->is_active);

        $springfields = City::query()->where('country_id', $tl->id)->where('name', 'Springfield')->get();
        $this->assertCount(2, $springfields);
        $this->assertNotEquals($springfields[0]->state_code, $springfields[1]->state_code);
    }

    public function test_activate_requires_readiness(): void
    {
        $country = Country::query()->create([
            'iso2' => 'XX',
            'iso3' => 'XXX',
            'name' => 'Incomplete Land',
            'currency_code' => null,
            'default_locale' => null,
            'timezone' => null,
            'is_active' => false,
            'sort_order' => 50,
        ]);

        $readiness = CountryReadiness::check($country);
        $this->assertFalse($readiness['ready']);
        $this->assertNotEmpty($readiness['missing']);

        $result = CountryVisibility::sync([$country->id]);
        $this->assertArrayHasKey($country->id, $result['errors']);
        $this->assertFalse($country->fresh()->is_active);
    }

    public function test_visibility_save_and_permissions(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $this->assertTrue($pk->is_active);

        $super = $this->makeSuperAdmin();
        $this->actingAs($super)
            ->get(route('admin.countries.visibility'))
            ->assertOk();

        $admin = $this->makeAdmin(['country_id' => null]);
        $this->actingAs($admin)
            ->put(route('admin.countries.visibility.update'), [
                'enabled' => [$pk->id],
            ])
            ->assertRedirect();

        $countryAdmin = $this->makeUserWithRole('country_admin', ['country_id' => $pk->id]);
        $this->actingAs($countryAdmin)
            ->get(route('admin.countries.visibility'))
            ->assertForbidden();

        $orgAdmin = $this->makeUserWithRole('organization_admin', ['country_id' => $pk->id]);
        $this->actingAs($orgAdmin)
            ->put(route('admin.countries.visibility.update'), ['enabled' => [$pk->id]])
            ->assertForbidden();
    }

    public function test_city_endpoint_scoped_and_capped(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $ae = Country::query()->where('iso2', 'AE')->first();

        $response = $this->getJson(route('countries.cities', $pk).'?search=La');
        $response->assertOk();
        $data = $response->json('data');
        $this->assertIsArray($data);
        $this->assertLessThanOrEqual(50, count($data));
        foreach ($data as $row) {
            $this->assertSame($pk->id, $row['country_id']);
        }

        if ($ae) {
            $foreignCity = City::query()->where('country_id', $ae->id)->first();
            if ($foreignCity) {
                $this->assertFalse(collect($data)->contains(fn ($r) => (int) $r['id'] === (int) $foreignCity->id));
            }
        }

        $api = $this->getJson('/api/v1/countries');
        $api->assertOk();
        foreach ($api->json('data') as $row) {
            $c = Country::query()->find($row['id']);
            $this->assertTrue($c->is_active);
            $this->assertFalse($c->wind_down);
        }
    }

    public function test_disabled_country_blocks_new_listing_keeps_old(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $listing = Listing::query()->where('country_id', $pk->id)->first();
        $this->assertNotNull($listing);

        $pk->forceFill(['is_active' => false, 'wind_down' => false])->save();
        CountryCatalog::flush();

        $admin = $this->makeAdmin();
        $type = ListingType::query()->where('is_active', true)->firstOrFail();
        $this->actingAs($admin)
            ->post(route('admin.listings.store'), [
                'listing_type_id' => $type->id,
                'title' => 'Blocked country listing',
                'location' => 'Somewhere',
                'purpose' => 'sale',
                'price' => 1000,
                'price_unit' => 'total',
                'status' => 'draft',
                'country_id' => $pk->id,
            ])
            ->assertSessionHasErrors('country_id');

        $attrPayload = $listing->getAttribute('attributes') ?? [];
        if (! is_array($attrPayload)) {
            $attrPayload = [];
        }

        $this->actingAs($admin)
            ->put(route('admin.listings.update', $listing), [
                'listing_type_id' => $listing->listing_type_id,
                'title' => $listing->title.' (kept)',
                'location' => $listing->location,
                'purpose' => $listing->purpose,
                'price' => $listing->price,
                'price_unit' => $listing->price_unit ?? 'total',
                'status' => $listing->status,
                'country_id' => $pk->id,
                'city_id' => $listing->city_id,
                'currency' => $listing->currency ?? 'PKR',
                'attributes' => $attrPayload ?: ['bedrooms' => 2, 'bathrooms' => 1],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame($pk->id, (int) $listing->fresh()->country_id);
        $this->assertStringContainsString('(kept)', $listing->fresh()->title);
    }

    public function test_city_of_another_country_rejected(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $aeCity = City::query()->whereHas('country', fn ($q) => $q->where('iso2', 'AE'))->first();
        if (! $aeCity) {
            $this->markTestSkipped('No AE city seeded');
        }

        $admin = $this->makeAdmin();
        $type = ListingType::query()->where('is_active', true)->firstOrFail();
        $this->actingAs($admin)
            ->post(route('admin.listings.store'), [
                'listing_type_id' => $type->id,
                'title' => 'Wrong city',
                'location' => 'Somewhere',
                'purpose' => 'sale',
                'price' => 1000,
                'price_unit' => 'total',
                'status' => 'draft',
                'country_id' => $pk->id,
                'city_id' => $aeCity->id,
            ])
            ->assertSessionHasErrors('city_id');
    }

    public function test_wind_down_rules(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $listing = Listing::query()
            ->where('country_id', $pk->id)
            ->where('status', 'published')
            ->whereHas('roomTypes')
            ->first();
        $this->assertNotNull($listing);
        $roomType = $listing->roomTypes()->firstOrFail();
        $room = $listing->rooms()->first();

        $user = $this->makeClient();
        Booking::query()->create([
            'country_id' => $pk->id,
            'user_id' => $user->id,
            'listing_id' => $listing->id,
            'room_type_id' => $roomType->id,
            'room_id' => $room?->id,
            'check_in' => now()->addDays(2)->toDateString(),
            'check_out' => now()->addDays(4)->toDateString(),
            'guests' => 1,
            'total' => 100,
            'currency' => 'PKR',
            'status' => 'confirmed',
        ]);

        $result = CountryVisibility::sync([], false);
        $this->assertArrayHasKey($pk->id, $result['errors']);

        $result = CountryVisibility::sync([], true);
        $this->assertContains($pk->id, $result['wind_down']);
        $pk->refresh();
        $this->assertTrue($pk->wind_down);
        $this->assertTrue($pk->is_active);
        $this->assertFalse($pk->acceptsNewListings());

        $this->get(route('properties'))->assertOk();
        $this->assertFalse(
            Listing::query()
                ->published()
                ->whereHas('country', fn ($c) => $c->where('is_active', true)->where('wind_down', false))
                ->where('id', $listing->id)
                ->exists()
        );
    }

    public function test_scoped_user_cannot_see_other_countries_in_api(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $countryAdmin = $this->makeUserWithRole('country_admin', ['country_id' => $pk->id]);

        Sanctum::actingAs($countryAdmin);
        $response = $this->getJson('/api/v1/countries');
        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertSame([$pk->id], $ids);
    }

    public function test_dropdown_only_active_countries(): void
    {
        $inactive = Country::query()->create([
            'iso2' => 'ZZ',
            'name' => 'Zedland',
            'currency_code' => null,
            'default_locale' => null,
            'timezone' => null,
            'is_active' => false,
            'sort_order' => 99,
        ]);

        $list = CountryCatalog::selectable();
        $this->assertFalse($list->contains(fn (Country $c) => (int) $c->id === (int) $inactive->id));
        $this->assertTrue($list->contains(fn (Country $c) => $c->iso2 === 'PK'));
    }
}
