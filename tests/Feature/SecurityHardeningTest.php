<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use App\Policies\ListingPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\Support\CreatesListings;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use CreatesListings;
    use RefreshDatabase;

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => 'throttle@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login'), [
            'email' => 'throttle@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    public function test_inquiry_stores_property_id_from_listing_form(): void
    {
        $listing = $this->makeListing([
            'title' => 'Linked Villa',
            'location' => 'Lahore',
        ]);

        $response = $this->post(route('contact.store'), [
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'message' => 'Please contact me about this listing.',
            'listing_id' => $listing->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_inquiries', [
            'email' => 'buyer@example.com',
            'listing_id' => $listing->id,
        ]);
    }

    public function test_cannot_demote_or_delete_the_last_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $demote = $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'role' => 'user',
            ]);

        $demote->assertRedirect(route('admin.users.edit', $admin));
        $demote->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);

        $delete = $this->actingAs($admin)
            ->from(route('admin.users.show', $admin))
            ->delete(route('admin.users.destroy', $admin));

        $delete->assertRedirect(route('admin.users.show', $admin));
        $delete->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_is_denied_by_property_policy(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->assertFalse(Gate::forUser($user)->allows('viewAny', Listing::class));
        $this->assertFalse((new ListingPolicy)->viewAny($user));
    }
}
