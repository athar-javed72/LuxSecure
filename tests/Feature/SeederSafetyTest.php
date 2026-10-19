<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Page;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SeasonalPrice;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class SeederSafetyTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbac;

    public function test_production_database_seeder_only_creates_roles_not_demo_accounts(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('db:seed', [
            '--class' => DatabaseSeeder::class,
            '--force' => true,
        ])->assertSuccessful();

        $this->assertTrue(Role::query()->where('name', 'super_admin')->exists());
        $this->assertTrue(Role::query()->where('name', 'client')->exists());
        $this->assertDatabaseMissing('users', ['email' => 'admin@luxsecure.com']);
        $this->assertSame(0, Listing::query()->count());
        $this->assertSame(0, RoomType::query()->count());
        $this->assertSame(0, Page::query()->count());
        $this->assertSame('Maskanoor Group', Setting::getValue('site.name'));
        $this->assertDatabaseHas('countries', ['iso2' => 'PK']);
    }

    public function test_local_demo_seeder_creates_hotel_inventory_for_booking_flow(): void
    {
        $this->app['env'] = 'testing';

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'admin@luxsecure.com']);

        $hotel = Listing::query()->where('slug', 'blue-area-boutique-hotel')->first();
        $this->assertNotNull($hotel);

        $roomTypes = RoomType::query()->where('listing_id', $hotel->id)->get();
        $this->assertGreaterThanOrEqual(2, $roomTypes->count());
        $this->assertGreaterThanOrEqual(5, Room::query()->where('listing_id', $hotel->id)->count());
        $this->assertGreaterThanOrEqual(2, SeasonalPrice::query()->count());
    }

    public function test_local_seeder_creates_draft_cms_policy_pages_and_placeholder_contact_settings(): void
    {
        $this->app['env'] = 'testing';

        $this->seed(DatabaseSeeder::class);

        foreach (['privacy', 'terms', 'refund'] as $type) {
            $page = Page::query()->where('type', $type)->whereNull('country_id')->first();
            $this->assertNotNull($page, "Missing global CMS page type {$type}");
            $this->assertFalse((bool) $page->is_published);
            $this->assertStringContainsString('[PLACEHOLDER:', (string) $page->body);
        }

        $this->assertSame('pmobie264@gmail.com', Setting::getValue('site.email'));
        $this->assertSame('+923228439536', Setting::getValue('site.phone'));
        $this->assertSame('Available 24 hours', Setting::getValue('site.hours'));
        $this->assertSame('Maskanoor Group', Setting::getValue('site.name'));
    }

    public function test_create_super_admin_command_enforces_strong_password_and_creates_first_admin(): void
    {
        $this->seedRbac();

        $this->artisan('luxsecure:create-super-admin', [
            '--email' => 'weak@example.com',
            '--name' => 'Weak',
            '--password' => 'password',
        ])->assertFailed();

        $this->artisan('luxsecure:create-super-admin', [
            '--email' => 'owner@example.com',
            '--name' => 'Owner',
            '--password' => 'Str0ng!Passw0rd',
        ])->assertSuccessful();

        $user = User::query()->where('email', 'owner@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('super_admin'));
        $this->assertTrue($user->is_active);
        $this->assertNotNull($user->email_verified_at);

        $this->artisan('luxsecure:create-super-admin', [
            '--email' => 'second@example.com',
            '--name' => 'Second',
            '--password' => 'Str0ng!Passw0rd',
        ])->assertFailed();
    }
}
