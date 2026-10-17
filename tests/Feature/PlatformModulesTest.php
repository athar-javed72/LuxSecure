<?php

namespace Tests\Feature;

use App\Models\AgentProfile;
use App\Models\BlogPost;
use App\Models\ContactInquiry;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\ListingImage;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Tests\Support\CreatesListings;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class PlatformModulesTest extends TestCase
{
    use CreatesListings;
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRbac();
        $this->seed(\Database\Seeders\SettingsSeeder::class);
    }

    public function test_home_uses_agent_profiles_and_cms_content(): void
    {
        $agent = $this->makeAgent(['email' => 'profile-agent@example.com']);
        AgentProfile::create([
            'user_id' => $agent->id,
            'phone' => '+92 300 9998877',
            'agency' => 'Estate Noir Realty',
            'is_verified' => true,
            'is_featured' => true,
            'bio' => 'Luxury specialist',
        ]);

        BlogPost::create([
            'title' => 'Market update',
            'slug' => 'market-update',
            'excerpt' => 'Brief',
            'body' => 'Body',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $agent->id,
        ]);

        Faq::create([
            'question' => 'Is LuxSecure verified?',
            'answer' => 'Yes.',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Estate Noir Realty')
            ->assertSee('Market update')
            ->assertSee('Is LuxSecure verified?')
            ->assertDontSee('Ayesha Khan');
    }

    public function test_inquiry_converts_to_lead_and_agent_scoped(): void
    {
        $agent = $this->makeAgent();
        $other = $this->makeAgent(['email' => 'other-a@example.com']);
        $listing = $this->makeListing(['agent_id' => $agent->id, 'title' => 'Lead Villa']);

        $inquiry = ContactInquiry::create([
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'message' => 'Interested',
            'listing_id' => $listing->id,
            'is_read' => false,
        ]);

        $this->actingAs($agent)
            ->post(route('admin.leads.convert', $inquiry))
            ->assertRedirect();

        $lead = Lead::first();
        $this->assertNotNull($lead);
        $this->assertSame($agent->id, $lead->assigned_agent_id);

        $this->actingAs($other)->get(route('admin.leads.edit', $lead))->assertForbidden();
        $this->actingAs($agent)->get(route('admin.leads.edit', $lead))->assertOk();
    }

    public function test_support_ticket_permissions_and_reply(): void
    {
        $support = $this->makeUserWithRole('support', ['email' => 'support@example.com']);
        $client = $this->makeClient(['email' => 'ticket-client@example.com']);

        $ticket = Ticket::create([
            'subject' => 'Help needed',
            'body' => 'Cannot login',
            'user_id' => $client->id,
            'assigned_to' => $support->id,
            'status' => 'open',
            'priority' => 'high',
        ]);

        $this->actingAs($support)
            ->post(route('admin.tickets.reply', $ticket), ['body' => 'We are on it'])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'body' => 'We are on it',
        ]);

        $this->actingAs($this->makeAgent(['email' => 'no-tickets@example.com']))
            ->get(route('admin.tickets.show', $ticket))
            ->assertForbidden();
    }

    public function test_cms_and_settings_are_permission_protected(): void
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent(['email' => 'cms-agent@example.com']);

        $this->actingAs($agent)->get(route('admin.cms.index'))->assertForbidden();
        $this->actingAs($agent)->get(route('admin.settings.edit'))->assertForbidden();

        $this->actingAs($admin)->get(route('admin.cms.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.settings.edit'))->assertOk();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'settings' => [
                'site.name' => 'LuxSecure Pro',
            ],
        ])->assertRedirect();

        $this->assertSame('LuxSecure Pro', Setting::getValue('site.name'));
    }

    public function test_activity_log_records_admin_listing_change(): void
    {
        $admin = $this->makeAdmin();
        $listing = $this->makeListing(['title' => 'Before']);

        $this->actingAs($admin)->put(route('admin.listings.update', $listing), [
            'listing_type_id' => $listing->listing_type_id,
            'title' => 'After Activity',
            'slug' => $listing->slug,
            'location' => $listing->location,
            'purpose' => $listing->purpose,
            'price' => $listing->price,
            'price_unit' => $listing->price_unit,
            'currency' => $listing->currency,
            'status' => $listing->status,
            'city_id' => $listing->city_id,
            'description' => $listing->description,
            'attributes' => $listing->attributeValues(),
        ])->assertRedirect();

        $this->assertTrue(Activity::query()->where('subject_type', \App\Models\Listing::class)->exists());

        $this->actingAs($admin)->get(route('admin.activity.index'))->assertOk();
        $this->actingAs($this->makeAgent(['email' => 'no-log@example.com']))
            ->get(route('admin.activity.index'))
            ->assertForbidden();
    }

    public function test_csv_export_and_bulk_listings(): void
    {
        $admin = $this->makeAdmin();
        $a = $this->makeListing(['title' => 'Export A', 'status' => 'draft']);
        $b = $this->makeListing(['title' => 'Export B', 'status' => 'draft']);

        $this->actingAs($admin)
            ->get(route('admin.listings.export'))
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->actingAs($admin)->post(route('admin.listings.bulk'), [
            'ids' => [$a->id, $b->id],
            'action' => 'status',
            'status' => 'published',
        ])->assertRedirect();

        $this->assertSame('published', $a->fresh()->status);
        $this->assertSame('published', $b->fresh()->status);
    }

    public function test_gallery_image_delete_and_reorder(): void
    {
        $admin = $this->makeAdmin();
        $listing = $this->makeListing();

        Storage::fake(\App\Support\StorageDisk::uploads());
        Storage::disk(\App\Support\StorageDisk::uploads())->put('listings/a.jpg', 'fake-image-a');
        Storage::disk(\App\Support\StorageDisk::uploads())->put('listings/b.jpg', 'fake-image-b');

        $img1 = ListingImage::create([
            'listing_id' => $listing->id,
            'path' => 'listings/a.jpg',
            'sort_order' => 0,
        ]);
        $img2 = ListingImage::create([
            'listing_id' => $listing->id,
            'path' => 'listings/b.jpg',
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)->post(route('admin.listings.images.reorder', $listing), [
            'order' => [$img2->id, $img1->id],
        ])->assertRedirect();

        $this->assertSame(0, $img2->fresh()->sort_order);
        $this->assertSame(1, $img1->fresh()->sort_order);

        $this->actingAs($admin)
            ->delete(route('admin.listings.images.destroy', [$listing, $img1]))
            ->assertRedirect();

        $this->assertDatabaseMissing('listing_images', ['id' => $img1->id]);
        Storage::disk(\App\Support\StorageDisk::uploads())->assertMissing('listings/a.jpg');
    }

    public function test_users_and_leads_csv_export(): void
    {
        $admin = $this->makeAdmin();
        Lead::create([
            'name' => 'CSV Lead',
            'email' => 'csv@example.com',
            'status' => 'new',
        ]);

        $this->actingAs($admin)->get(route('admin.users.export'))->assertOk();
        $this->actingAs($admin)->get(route('admin.leads.export'))->assertOk();
    }
}
