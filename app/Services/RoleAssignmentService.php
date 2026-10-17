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
        $allowlist = $this->allowlistFor($actor);

        return array_values(array_intersect($roleNames, $allowlist));
    }

    /**
     * @return list<string>
     */
    public function allowlistFor(User $actor): array
    {
        if ($actor->isSuperAdmin()) {
            return array_values(config('rbac.roles', []));
        }

        $map = config('rbac.assignable_roles', []);

        foreach ($actor->getRoleNames() as $role) {
            if (isset($map[$role])) {
                return array_values($map[$role]);
            }
        }

        return [];
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

        if (! $actor->can('users.assign_roles')) {
            return;
        }

        $filtered = $this->filterAssignableRoles($actor, $roleNames);

        if (in_array('super_admin', $roleNames, true) && ! $actor->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'roles' => 'You may not assign the super_admin role.',
            ]);
        }

        if (in_array('admin', $roleNames, true) && ! $actor->isSuperAdmin() && ! in_array('admin', $this->allowlistFor($actor), true)) {
            throw ValidationException::withMessages([
                'roles' => 'You may not assign the admin role.',
            ]);
        }

        if (in_array('super_admin', $target->getRoleNames()->all(), true)
            && ! in_array('super_admin', $filtered, true)
            && $target->isLastSuperAdmin()) {
            throw ValidationException::withMessages([
                'roles' => 'Cannot demote the last remaining super admin.',
            ]);
        }

        $target->syncRoles($filtered);
    }

    /**
     * @param  array<int, string>  $permissionNames
     */
    public function syncDirectPermissions(User $actor, User $target, array $permissionNames): void
    {
        if (! $actor->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'permissions' => 'Only a super admin may assign direct permissions.',
            ]);
        }

        if ($target->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'permissions' => 'Only a super admin can modify another super admin.',
            ]);
        }

        if (! $actor->can('users.assign_permissions')) {
            return;
        }

        $filtered = [];
        foreach (array_values(array_unique($permissionNames)) as $name) {
            if ($this->isForbiddenDirectPermission($name) && ! $actor->isSuperAdmin()) {
                continue;
            }
            // Super admin may grant restricted permissions; still block nothing for super_admin per requirement
            // "except by super_admin".
            $filtered[] = $name;
        }

        $target->syncPermissions($filtered);
    }

    public function isForbiddenDirectPermission(string $name): bool
    {
        if ($name === 'panel.access') {
            return true;
        }

        if (str_ends_with($name, '.any')) {
            return true;
        }

        foreach (['users.', 'roles.', 'settings.'] as $prefix) {
            if (str_starts_with($name, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function assignableRolesFor(User $actor): Collection
    {
        return Role::query()
            ->whereIn('name', $this->allowlistFor($actor))
            ->orderBy('name')
            ->get();
    }

    public function assignablePermissionsFor(User $actor): Collection
    {
        if (! $actor->isSuperAdmin()) {
            return collect();
        }

        return Permission::query()->orderBy('name')->get();
    }
}
