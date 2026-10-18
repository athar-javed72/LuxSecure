<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\User;
use App\Policies\PaymentPolicy;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class CountryScopingRemediationTest extends TestCase
{
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->seedRbac();
    }

    public function test_country_admin_cannot_list_foreign_listings(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $ae = Country::query()->firstOrCreate(
            ['iso2' => 'AE'],
            [
                'name' => 'United Arab Emirates',
                'currency_code' => 'AED',
                'default_locale' => 'en',
                'timezone' => 'Asia/Dubai',
                'phone_code' => '+971',
                'area_units' => ['sq_m'],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $admin = User::factory()->create([
            'country_id' => $pk->id,
            'email_verified_at' => now(),
            'is_active' => true,
            'two_factor_secret' => encrypt('TEST2FASECRET233'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovercode1'])),
            'two_factor_confirmed_at' => now(),
        ]);
        $admin->assignRole('country_admin');

        $pkListing = Listing::query()->where('country_id', $pk->id)->firstOrFail();
        $foreign = $pkListing->replicate();
        $foreign->slug = 'ae-scope-list-'.uniqid();
        $foreign->country_id = $ae->id;
        $foreign->title = 'Foreign listing';
        $foreign->save();

        $this->actingAs($admin)
            ->get(route('admin.listings.index'))
            ->assertOk()
            ->assertSee($pkListing->title)
            ->assertDontSee($foreign->title);
    }

    public function test_country_admin_cannot_approve_foreign_payment(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $ae = Country::query()->firstOrCreate(
            ['iso2' => 'AE'],
            [
                'name' => 'United Arab Emirates',
                'currency_code' => 'AED',
                'default_locale' => 'en',
                'timezone' => 'Asia/Dubai',
                'phone_code' => '+971',
                'area_units' => ['sq_m'],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $admin = User::factory()->create([
            'country_id' => $pk->id,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $admin->assignRole('country_admin');

        $payer = $this->makeClient(['country_id' => $ae->id]);
        $payment = Payment::query()->create([
            'country_id' => $ae->id,
            'user_id' => $payer->id,
            'payable_type' => Listing::class,
            'payable_id' => Listing::query()->first()->id,
            'gateway' => 'bank_transfer',
            'amount' => 1000,
            'currency' => 'AED',
            'status' => 'pending',
        ]);

        $this->assertFalse(app(PaymentPolicy::class)->approve($admin, $payment));
    }

    public function test_hotel_manager_owns_only_managed_listings(): void
    {
        $listing = Listing::query()->whereNotNull('manager_id')->first()
            ?? Listing::query()->where('slug', 'blue-area-boutique-hotel')->firstOrFail();

        $manager = $this->makeHotelManager([
            'organization_id' => $listing->organization_id,
            'country_id' => $listing->country_id,
        ]);
        $listing->update(['manager_id' => $manager->id]);

        $other = $listing->replicate();
        $other->slug = 'other-mgr-'.uniqid();
        $other->manager_id = null;
        $other->title = 'Sibling branch';
        $other->save();

        $this->assertTrue($manager->ownsListing($listing->fresh()));
        $this->assertFalse($manager->ownsListing($other->fresh()));
    }

    public function test_locale_switch_does_not_rewrite_country_admin_country(): void
    {
        $pk = Country::query()->where('iso2', 'PK')->firstOrFail();
        $ae = Country::query()->firstOrCreate(
            ['iso2' => 'AE'],
            [
                'name' => 'United Arab Emirates',
                'currency_code' => 'AED',
                'default_locale' => 'en',
                'timezone' => 'Asia/Dubai',
                'phone_code' => '+971',
                'area_units' => ['sq_m'],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $admin = User::factory()->create([
            'country_id' => $pk->id,
            'email_verified_at' => now(),
            'is_active' => true,
            'two_factor_secret' => encrypt('TEST2FASECRET233'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovercode1'])),
            'two_factor_confirmed_at' => now(),
        ]);
        $admin->assignRole('country_admin');

        $this->actingAs($admin)
            ->post(route('locale.update'), [
                'country_id' => $ae->id,
                'currency' => 'AED',
                'locale' => 'en',
            ])
            ->assertRedirect();

        $this->assertSame((int) $pk->id, (int) $admin->fresh()->country_id);
        $this->assertSame((int) $pk->id, (int) session('country_id'));
    }

    public function test_organization_admin_has_rooms_own_permissions(): void
    {
        $user = $this->makeUserWithRole('organization_admin');
        $this->assertTrue($user->can('rooms.view.own'));
        $this->assertTrue($user->can('rooms.create'));
        $this->assertTrue($user->can('rooms.update.own'));
    }
}
