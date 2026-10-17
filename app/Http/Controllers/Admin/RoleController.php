<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRolePermissionsRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Support\Rbac;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::query()->withCount(['users', 'permissions'])->orderBy('name')->paginate(20);

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorize('create', Role::class);

        return view('admin.roles.create', [
            'groupedPermissions' => Rbac::groupedPermissionsFromDb(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        $role = Role::create([
            'name' => $request->string('name')->toString(),
            'guard_name' => config('auth.defaults.guard', 'web'),
            'is_system' => false,
        ]);

        $role->syncPermissions($request->input('permissions', []));
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role): View
    {
        $this->authorize('update', $role);

        $role->load('permissions');

        return view('admin.roles.edit', [
            'role' => $role,
            'groupedPermissions' => Rbac::groupedPermissionsFromDb(),
            'selected' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        if ($role->is_system && $role->name !== $request->input('name')) {
            return back()->with('error', 'System role names cannot be renamed.');
        }

        $role->update([
            'name' => $request->string('name')->toString(),
        ]);

        // Only super_admin reaches here for permission grants of any kind via policy.
        $role->syncPermissions($request->input('permissions', []));
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        $this->authorize('delete', $role);

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }

    public function matrix(Role $role): View
    {
        $this->authorize('managePermissions', $role);

        $role->load('permissions');

        return view('admin.roles.matrix', [
            'role' => $role,
            'groupedPermissions' => Rbac::groupedPermissionsFromDb(),
            'selected' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function updateMatrix(UpdateRolePermissionsRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('managePermissions', $role);

        $role->syncPermissions($request->input('permissions', []));
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.matrix', $role)->with('success', 'Permission matrix updated.');
    }
}
