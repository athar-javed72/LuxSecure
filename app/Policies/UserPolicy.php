<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksCountryScope;

class UserPolicy
{
    use ChecksCountryScope;

    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view') && $this->inCountryScope($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        if ($model->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->can('users.update') && $this->inCountryScope($user, $model);
    }

    public function delete(User $user, User $model): bool
    {
        if ($model->id === $user->id) {
            return false;
        }

        if ($model->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        if ($model->isLastSuperAdmin()) {
            return false;
        }

        return $user->can('users.delete') && $this->inCountryScope($user, $model);
    }

    public function assignRoles(User $user): bool
    {
        return $user->can('users.assign_roles');
    }

    public function assignPermissions(User $user): bool
    {
        return $user->can('users.assign_permissions');
    }
}
