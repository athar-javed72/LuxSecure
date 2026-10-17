<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesListings;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use CreatesListings;
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRbac();
    }

    public function test_client_cannot_access_admin_panel(): void
    {
        $this->actingAs($this->makeClient())->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_agent_can_access_panel_but_not_users(): void
    {
        $agent = $this->makeAgent();

        $this->actingAs($agent)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($agent)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_agent_only_sees_own_listings(): void
    {
        $agent = $this->makeAgent();
        $other = $this->makeAgent(['email' => 'other-agent@example.com']);

        $own = $this->makeListing(['title' => 'Own Listing', 'agent_id' => $agent->id]);
        $this->makeListing(['title' => 'Other Listing', 'agent_id' => $other->id]);

        $this->actingAs($agent)
            ->get(route('admin.listings.index'))
            ->assertOk()
            ->assertSee('Own Listing')
            ->assertDontSee('Other Listing');

        $this->actingAs($agent)->get(route('admin.listings.show', $own))->assertOk();
    }

    public function test_admin_cannot_create_roles_or_open_permission_matrix(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get(route('admin.roles.create'))->assertForbidden();
        $role = Role::findByName('agent');
        $this->actingAs($admin)->get(route('admin.roles.matrix', $role))->assertForbidden();
    }

    public function test_super_admin_can_update_permission_matrix(): void
    {
        $super = $this->makeSuperAdmin();
        $role = Role::findByName('support');

        $response = $this->actingAs($super)->put(route('admin.roles.matrix.update', $role), [
            'permissions' => ['panel.access', 'inquiries.view.any'],
        ]);

        $response->assertRedirect(route('admin.roles.matrix', $role));
        $this->assertTrue($role->fresh()->hasPermissionTo('panel.access'));
        $this->assertTrue($role->fresh()->hasPermissionTo('inquiries.view.any'));
        $this->assertFalse($role->fresh()->hasPermissionTo('users.delete'));
    }

    public function test_admin_cannot_assign_direct_permissions(): void
    {
        $admin = $this->makeAdmin();
        $target = $this->makeClient(['email' => 'target@example.com']);

        // Only super_admin may assign direct permissions — admin input is ignored entirely.
        $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'is_active' => 1,
            'roles' => ['client'],
            'permissions' => ['roles.create', 'panel.access', 'listings.view.own'],
        ])->assertRedirect();

        $target->refresh();
        $this->assertFalse($target->hasDirectPermission('roles.create'));
        $this->assertFalse($target->hasDirectPermission('panel.access'));
        $this->assertFalse($target->hasDirectPermission('listings.view.own'));
        $this->assertCount(0, $target->getDirectPermissions());
    }

    public function test_admin_cannot_edit_super_admin(): void
    {
        $admin = $this->makeAdmin();
        $super = $this->makeSuperAdmin(['email' => 'super@example.com']);

        $this->actingAs($admin)->get(route('admin.users.edit', $super))->assertForbidden();
        $this->actingAs($admin)->put(route('admin.users.update', $super), [
            'name' => 'Hacked',
            'email' => $super->email,
            'is_active' => 1,
            'roles' => ['client'],
        ])->assertForbidden();
    }

    public function test_system_roles_cannot_be_deleted(): void
    {
        $super = $this->makeSuperAdmin();
        $role = Role::findByName('client');

        $this->actingAs($super)
            ->from(route('admin.roles.index'))
            ->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['name' => 'client']);
    }

    public function test_last_super_admin_cannot_be_demoted_or_deleted(): void
    {
        $super = $this->makeSuperAdmin();

        $this->actingAs($super)->put(route('admin.users.update', $super), [
            'name' => $super->name,
            'email' => $super->email,
            'is_active' => 1,
            'roles' => ['admin'],
        ])->assertSessionHasErrors('roles');

        $this->assertTrue($super->fresh()->hasRole('super_admin'));

        $this->actingAs($super)->delete(route('admin.users.destroy', $super))->assertForbidden();
    }

    public function test_support_cannot_manage_listings(): void
    {
        $support = $this->makeUserWithRole('support');

        $this->actingAs($support)->get(route('admin.listings.index'))->assertForbidden();
        $this->actingAs($support)->get(route('admin.inquiries.index'))->assertOk();
    }

    public function test_registration_only_assigns_client_role(): void
    {
        $this->post(route('register'), [
            'name' => 'New Client',
            'email' => 'newclient@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $user = \App\Models\User::where('email', 'newclient@example.com')->first();
        $this->assertTrue($user->hasRole('client'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertFalse($user->can('panel.access'));
    }
}
