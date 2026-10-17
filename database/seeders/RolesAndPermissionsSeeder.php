<?php

namespace Database\Seeders;

use App\Support\Rbac;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

        foreach (Rbac::allPermissionNames() as $name) {
            Permission::findOrCreate($name, $guard);
        }

        foreach (config('rbac.roles', []) as $roleName) {
            $role = Role::findOrCreate($roleName, $guard);
            $role->is_system = in_array($roleName, config('rbac.system_roles', []), true);
            $role->save();

            $permissions = Rbac::expandRolePermissions($roleName);
            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
