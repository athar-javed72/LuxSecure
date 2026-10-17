<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('roles.view');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.view');
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() && $user->can('roles.create');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->isSuperAdmin() && $user->can('roles.update');
    }

    public function delete(User $user, Role $role): bool
    {
        if ($role->is_system) {
            return false;
        }

        return $user->isSuperAdmin() && $user->can('roles.delete');
    }

    public function managePermissions(User $user, ?Role $role = null): bool
    {
        return $user->isSuperAdmin() && $user->can('roles.manage_permissions');
    }
}
