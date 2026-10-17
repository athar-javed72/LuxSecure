<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAssignmentService
{
    /**
     * @param  array<int, string>  $roleNames
     * @return array<int, string>
     */
    public function filterAssignableRoles(User $actor, array $roleNames): array
    {
        $roleNames = array_values(array_unique($roleNames));

        if ($actor->isSuperAdmin()) {
            return $roleNames;
        }

        $owned = $actor->getRoleNames()->all();

        return array_values(array_intersect($roleNames, $owned));
    }

    /**
     * @param  array<int, string>  $permissionNames
     * @return array<int, string>
     */
    public function filterAssignablePermissions(User $actor, array $permissionNames): array
    {
        $permissionNames = array_values(array_unique($permissionNames));

        if ($actor->isSuperAdmin()) {
            return $permissionNames;
        }

        $owned = $actor->getAllPermissions()->pluck('name')->all();

        return array_values(array_intersect($permissionNames, $owned));
    }

    /**
     * @param  array<int, string>  $roleNames
     */
    public function syncRoles(User $actor, User $target, array $roleNames): void
    {
        if ($target->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'roles' => 'Only a super admin can modify another super admin.',
            ]);
        }

        $filtered = $this->filterAssignableRoles($actor, $roleNames);

        if (in_array('super_admin', $target->getRoleNames()->all(), true)
            && ! in_array('super_admin', $filtered, true)
            && $target->isLastSuperAdmin()) {
            throw ValidationException::withMessages([
                'roles' => 'Cannot demote the last remaining super admin.',
            ]);
        }

        if (! $actor->isSuperAdmin() && in_array('super_admin', $filtered, true) && ! $actor->hasRole('super_admin')) {
            $filtered = array_values(array_diff($filtered, ['super_admin']));
        }

        if (! $actor->can('users.assign_roles')) {
            return;
        }

        $target->syncRoles($filtered);
    }

    /**
     * @param  array<int, string>  $permissionNames
     */
    public function syncDirectPermissions(User $actor, User $target, array $permissionNames): void
    {
        if ($target->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'permissions' => 'Only a super admin can modify another super admin.',
            ]);
        }

        if (! $actor->can('users.assign_permissions')) {
            return;
        }

        // Non-super-admins may only grant permissions they themselves hold.
        $filtered = $this->filterAssignablePermissions($actor, $permissionNames);

        // Super admin may grant any; non-super may not grant permissions beyond their own (already filtered).
        if (! $actor->isSuperAdmin()) {
            // Already filtered to owned permissions.
        }

        $target->syncPermissions($filtered);
    }

    public function assignableRolesFor(User $actor): Collection
    {
        $query = Role::query()->orderBy('name');

        if (! $actor->isSuperAdmin()) {
            $query->whereIn('name', $actor->getRoleNames());
        }

        return $query->get();
    }

    public function assignablePermissionsFor(User $actor): Collection
    {
        if ($actor->isSuperAdmin()) {
            return Permission::query()->orderBy('name')->get();
        }

        return $actor->getAllPermissions()->sortBy('name')->values();
    }
}
