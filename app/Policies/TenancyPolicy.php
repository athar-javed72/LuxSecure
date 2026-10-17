<?php

namespace App\Policies;

use App\Models\Tenancy;
use App\Models\User;

class TenancyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('tenancies.view.any') || $user->can('tenancies.view.own');
    }

    public function view(User $user, Tenancy $tenancy): bool
    {
        if ($user->can('tenancies.view.any')) {
            return true;
        }

        if (! $user->can('tenancies.view.own')) {
            return false;
        }

        return (int) $tenancy->landlord_id === (int) $user->id
            || (int) $tenancy->tenant_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('tenancies.create');
    }

    public function update(User $user, Tenancy $tenancy): bool
    {
        if ($user->can('tenancies.update.any')) {
            return true;
        }

        return $user->can('tenancies.update.own')
            && (int) $tenancy->landlord_id === (int) $user->id;
    }

    public function delete(User $user, Tenancy $tenancy): bool
    {
        if ($user->can('tenancies.delete.any')) {
            return true;
        }

        return $user->can('tenancies.delete.own')
            && (int) $tenancy->landlord_id === (int) $user->id;
    }
}
