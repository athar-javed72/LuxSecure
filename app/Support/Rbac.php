<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

class Rbac
{
    /** @return array<int, string> */
    public static function allPermissionNames(): array
    {
        $names = [];
        foreach (config('rbac.permissions', []) as $group) {
            foreach ($group as $permission) {
                $names[] = $permission;
            }
        }

        return array_values(array_unique($names));
    }

    /** @return array<string, array<int, string>> */
    public static function groupedPermissionNames(): array
    {
        return config('rbac.permissions', []);
    }

    public static function expandRolePermissions(string $role): array
    {
        $map = config('rbac.role_permissions.'.$role, []);
        if ($map === ['*']) {
            return self::allPermissionNames();
        }

        $all = self::allPermissionNames();
        $resolved = [];

        foreach ($map as $pattern) {
            if (str_ends_with($pattern, '.*')) {
                $prefix = substr($pattern, 0, -1);
                foreach ($all as $permission) {
                    if (str_starts_with($permission, $prefix)) {
                        $resolved[] = $permission;
                    }
                }
            } else {
                $resolved[] = $pattern;
            }
        }

        return array_values(array_unique($resolved));
    }

    public static function groupedPermissionsFromDb(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(function (Permission $permission) {
                return explode('.', $permission->name)[0] ?? 'other';
            });
    }
}
