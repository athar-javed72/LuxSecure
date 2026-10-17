<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\RoomType;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use PragmaRX\Google2FA\Google2FA;
use Tests\Support\CreatesListings;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use CreatesListings;
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRbac();
        Cache::flush();
    }

    public function test_register_login_logout_and_profile(): void
    {
        $register = $this->postJson('/api/v1/auth/register', [
            'name' => 'API User',
            'email' => 'api-user@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $register->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'email']]]);

        $token = $register->json('data.token');

        $this->withToken($token)
            ->getJson('/api/v1/profile')
            ->assertOk()
            ->assertJsonPath('data.email', 'api-user@example.com');

        $this->withToken($token)
            ->putJson('/api/v1/profile', ['name' => 'API Updated'])
            ->assertOk()
            ->assertJsonPath('data.name', 'API Updated');

        $this->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_listings_index_and_show_with_cache(): void
    {
        $listing = $this->makeListing(['title' => 'Cached Villa', 'slug' => 'cached-villa']);

        $this->getJson('/api/v1/listings')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['title' => 'Cached Villa']);

        $this->getJson('/api/v1/listings/cached-villa')
            ->assertOk()
            ->assertJsonPath('data.slug', 'cached-villa');

        // Legacy alias
        $this->getJson('/api/properties')
            ->assertOk()
            ->assertJsonStructure(['data', 'meta']);

        $this->assertNotNull($listing->id);
    }

    public function test_favorites_toggle_requires_auth(): void
    {
        $listing = $this->makeListing();

        $this->postJson('/api/v1/favorites/'.$listing->id)->assertUnauthorized();

        $user = $this->makeClient(['email' => 'fav@example.com']);
        Sanctum::actingAs($user, ['*']);

        $this->postJson('/api/v1/favorites/'.$listing->id)
            ->assertOk()
            ->assertJsonPath('data.favorited', true);

        $this->getJson('/api/v1/favorites')
            ->assertOk()
            ->assertJsonFragment(['id' => $listing->id]);
    }

    public function test_booking_create_and_list(): void
    {
        $manager = $this->makeHotelManager();
        $guest = $this->makeClient(['email' => 'book-api@example.com']);
        $listing = $this->makeListing([
            'purpose' => 'short_stay',
            'status' => 'published',
            'agent_id' => $manager->id,
            'owner_id' => $manager->id,
        ]);
        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => 'API Suite',
            'capacity' => 2,
            'base_price' => 5000,
            'is_active' => true,
        ]);
        Room::create([
            'room_type_id' => $roomType->id,
            'listing_id' => $listing->id,
            'number' => 'A1',
            'status' => 'available',
        ]);

        Sanctum::actingAs($guest, ['*']);

        $this->postJson('/api/v1/bookings', [
            'room_type_id' => $roomType->id,
            'check_in' => now()->addDays(2)->toDateString(),
            'check_out' => now()->addDays(4)->toDateString(),
            'guests' => 1,
        ])->assertCreated()->assertJsonPath('success', true);

        $this->getJson('/api/v1/bookings')
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    }

    public function test_validation_errors_are_consistent_json(): void
    {
        $this->postJson('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'validation_error')
            ->assertJsonStructure(['errors']);
    }

    public function test_admin_login_requires_two_factor_when_enabled(): void
    {
        $admin = $this->makeAdmin(['email' => 'admin2fa@example.com', 'password' => 'Password1!']);
        $secret = (new Google2FA)->generateSecretKey();
        $admin->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode(['abcd1234ef'])),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $challenge = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin2fa@example.com',
            'password' => 'Password1!',
        ])->assertOk()
            ->assertJsonPath('data.two_factor_required', true);

        $token = $challenge->json('data.two_factor_token');
        $code = (new Google2FA)->getCurrentOtp($secret);

        $this->postJson('/api/v1/auth/two-factor-challenge', [
            'two_factor_token' => $token,
            'code' => $code,
        ])->assertOk()
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_backup_command_runs(): void
    {
        $this->artisan('luxsecure:backup --storage')->assertSuccessful();
    }

    public function test_two_factor_service_confirms_for_admin(): void
    {
        $admin = $this->makeAdmin(['email' => 'setup2fa@example.com']);
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();
        $code = (new Google2FA)->getCurrentOtp($secret);

        $this->assertTrue($service->confirm($admin, $secret, $code));
        $this->assertTrue($admin->fresh()->hasTwoFactorEnabled());
    }
}
