<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class ToastNotificationTest extends TestCase
{
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    public function test_public_layout_renders_toast_host_with_session_flash(): void
    {
        $response = $this->withSession([
            'success' => 'Contact inquiry sent successfully.',
            'error' => 'Something went wrong.',
            'status' => 'Verification link sent.',
        ])->get(route('home'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('x-data="toastStack"', $html);
        $this->assertStringContainsString('fs-toast-host', $html);
        $this->assertStringContainsString('Contact inquiry sent successfully.', $html);
        $this->assertStringContainsString('Something went wrong.', $html);
        $this->assertStringContainsString('Verification link sent.', $html);
        $this->assertStringContainsString('&quot;duration&quot;:3000', $html);
        $this->assertSame(1, substr_count($html, 'fs-toast-host'));
    }

    public function test_admin_layout_renders_toast_host_with_session_flash(): void
    {
        $this->seedRbac();
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['success' => 'Settings saved.'])
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('x-data="toastStack"', $html);
        $this->assertStringContainsString('Settings saved.', $html);
        $this->assertStringContainsString('&quot;duration&quot;:3000', $html);
    }

    public function test_validation_errors_are_queued_as_toasts_once(): void
    {
        $response = $this->from(route('contact'))
            ->post(route('contact.store'), [
                'name' => '',
                'email' => 'not-an-email',
                'message' => '',
            ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHasErrors(['name', 'email', 'message']);

        $page = $this->get(route('contact'));
        $page->assertOk();
        $html = $page->getContent();

        $this->assertStringContainsString('x-data="toastStack"', $html);
        $this->assertStringContainsString('&quot;type&quot;:&quot;error&quot;', $html);
        $this->assertStringContainsString('The name field is required.', $html);
        $this->assertSame(1, substr_count($html, 'fs-toast-host'));
    }
}
