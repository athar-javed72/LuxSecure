<?php

namespace Tests\Feature;

use App\Models\ContactInquiry;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
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

        $response = $this->actingAs($admin)->post(route('admin.properties.store'), [
            'title' => 'Audit Test Villa',
            'location' => 'DHA Lahore',
            'type' => 'Villa',
            'price' => 50000000,
            'description' => 'A test villa',
            'bedrooms' => 4,
            'bathrooms' => 3,
            'is_active' => 1,
            'featured_image' => UploadedFile::fake()->create('villa.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect(route('admin.properties.index'));
        $this->assertDatabaseHas('properties', [
            'title' => 'Audit Test Villa',
            'location' => 'DHA Lahore',
            'type' => 'Villa',
        ]);
    }

    public function test_admin_can_update_a_property(): void
    {
        $admin = $this->makeAdmin();
        $property = Property::create([
            'title' => 'Old Title',
            'location' => 'Karachi',
            'type' => 'House',
            'price' => 30000000,
            'description' => 'Old',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.properties.update', $property), [
            'title' => 'Updated Title',
            'location' => 'Islamabad',
            'type' => 'Apartment',
            'price' => 40000000,
            'description' => 'Updated description',
            'bedrooms' => 2,
            'bathrooms' => 2,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.properties.index'));
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'title' => 'Updated Title',
            'location' => 'Islamabad',
            'type' => 'Apartment',
        ]);
    }

    public function test_admin_can_delete_a_property(): void
    {
        $admin = $this->makeAdmin();
        $property = Property::create([
            'title' => 'Delete Me',
            'location' => 'Lahore',
            'type' => 'Bungalow',
            'price' => 20000000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.properties.destroy', $property));

        $response->assertRedirect(route('admin.properties.index'));
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
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
