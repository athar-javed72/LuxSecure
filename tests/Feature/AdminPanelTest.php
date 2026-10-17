<?php

namespace Tests\Feature;

use App\Models\ContactInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\CreatesListings;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use CreatesListings;
    use RefreshDatabase;

    private function makeAdmin(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'admin',
            'email_verified_at' => now(),
        ], $overrides));
    }

    private function makeUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'user',
            'email_verified_at' => now(),
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_on_admin(): void
    {
        $this->get('/admin')->assertRedirect(route('login.form'));
    }

    public function test_non_admin_gets_403_on_admin(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_see_dashboard(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard', false)
            ->assertSee($admin->name, false);
    }

    public function test_admin_can_create_a_property(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $type = $this->makeListingType();
        $city = $this->makeCity('Lahore');

        $response = $this->actingAs($admin)->post(route('admin.listings.store'), [
            'listing_type_id' => $type->id,
            'title' => 'Audit Test Villa',
            'location' => 'DHA Lahore',
            'purpose' => 'sale',
            'price' => 50000000,
            'price_unit' => 'total',
            'currency' => 'PKR',
            'status' => 'published',
            'city_id' => $city->id,
            'description' => 'A test villa',
            'attributes' => [
                'bedrooms' => 4,
                'bathrooms' => 3,
            ],
            'featured_image' => UploadedFile::fake()->create('villa.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect(route('admin.listings.index'));
        $this->assertDatabaseHas('listings', [
            'title' => 'Audit Test Villa',
            'location' => 'DHA Lahore',
            'listing_type_id' => $type->id,
        ]);
    }

    public function test_admin_can_update_a_property(): void
    {
        $admin = $this->makeAdmin();
        $listing = $this->makeListing([
            'title' => 'Old Title',
            'location' => 'Karachi',
            'price' => 30000000,
        ]);
        $type = $listing->listingType;

        $response = $this->actingAs($admin)->put(route('admin.listings.update', $listing), [
            'listing_type_id' => $type->id,
            'title' => 'Updated Title',
            'location' => 'Islamabad',
            'purpose' => 'sale',
            'price' => 40000000,
            'price_unit' => 'total',
            'currency' => 'PKR',
            'status' => 'published',
            'description' => 'Updated description',
            'attributes' => [
                'bedrooms' => 2,
                'bathrooms' => 2,
            ],
        ]);

        $response->assertRedirect(route('admin.listings.index'));
        $this->assertDatabaseHas('listings', [
            'id' => $listing->id,
            'title' => 'Updated Title',
            'location' => 'Islamabad',
        ]);
    }

    public function test_admin_can_delete_a_property(): void
    {
        $admin = $this->makeAdmin();
        $listing = $this->makeListing(['title' => 'Delete Me']);

        $response = $this->actingAs($admin)->delete(route('admin.listings.destroy', $listing));

        $response->assertRedirect(route('admin.listings.index'));
        $this->assertSoftDeleted('listings', ['id' => $listing->id]);
    }

    public function test_admin_can_mark_an_inquiry_as_read(): void
    {
        $admin = $this->makeAdmin();
        $inquiry = ContactInquiry::create([
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'message' => 'Interested in a listing',
            'is_read' => false,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.inquiries.markRead', $inquiry));

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_inquiries', [
            'id' => $inquiry->id,
            'is_read' => 1,
        ]);
    }

    public function test_admin_cannot_delete_their_own_user_account(): void
    {
        $admin = $this->makeAdmin(['email' => 'self-admin@example.com']);

        $response = $this->actingAs($admin)->from(route('admin.users.show', $admin))
            ->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect(route('admin.users.show', $admin));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
