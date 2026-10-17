<?php

namespace App\Policies;

use App\Models\User;

class CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('cms.view');
    }

    public function view(User $user, mixed $model = null): bool
    {
        return $user->can('cms.view');
    }

    public function create(User $user): bool
    {
        return $user->can('cms.create');
    }

    public function update(User $user, mixed $model = null): bool
    {
        return $user->can('cms.update');
    }

    public function delete(User $user, mixed $model = null): bool
    {
        return $user->can('cms.delete');
    }
}
