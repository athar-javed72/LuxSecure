<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRbac();
    }

    public function test_register_creates_user_and_redirects(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login.form'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertTrue(\App\Models\User::where('email', 'test@example.com')->first()->hasRole('client'));
    }

    public function test_login_succeeds_with_valid_credentials(): void
    {
        $user = $this->makeClient([
            'email' => 'login@example.com',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'login@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('profile'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'wrong@example.com',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
