<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use App\Policies\Concerns\ChecksCountryScope;

class OrganizationPolicy
{
    use ChecksCountryScope;

    public function viewAny(User $user): bool
    {
        return $user->can('organizations.view');
    }

    public function view(User $user, Organization $organization): bool
    {
        if (! $user->can('organizations.view')) {
            return false;
        }

        if ($user->hasRole('organization_admin')) {
            return (int) $user->organization_id === (int) $organization->id;
        }

        return $this->inCountryScope($user, $organization);
    }

    public function create(User $user): bool
    {
        return $user->can('organizations.create') && ! $user->hasRole('organization_admin');
    }

    public function update(User $user, Organization $organization): bool
    {
        if (! $user->can('organizations.update')) {
            return false;
        }

        if ($user->hasRole('organization_admin')) {
            return (int) $user->organization_id === (int) $organization->id;
        }

        return $this->inCountryScope($user, $organization);
    }

    public function delete(User $user, Organization $organization): bool
    {
        if (! $user->can('organizations.delete') || $user->hasRole('organization_admin')) {
            return false;
        }

        return $this->inCountryScope($user, $organization);
    }
}
