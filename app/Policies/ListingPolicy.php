<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('listings.view.any') || $user->can('listings.view.own');
    }

    public function view(User $user, Listing $listing): bool
    {
        if ($user->can('listings.view.any') && $this->inCountryScope($user, $listing)) {
            return true;
        }

        return $user->can('listings.view.own') && $user->ownsListing($listing);
    }

    public function create(User $user): bool
    {
        return $user->can('listings.create');
    }

    public function update(User $user, Listing $listing): bool
    {
        if ($user->can('listings.update.any') && $this->inCountryScope($user, $listing)) {
            return true;
        }

        return $user->can('listings.update.own') && $user->ownsListing($listing);
    }

    public function delete(User $user, Listing $listing): bool
    {
        if ($user->can('listings.delete.any') && $this->inCountryScope($user, $listing)) {
            return true;
        }

        return $user->can('listings.delete.own') && $user->ownsListing($listing);
    }

    private function inCountryScope(User $user, Listing $listing): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->hasRole('admin') && blank($user->country_id)) {
            return true;
        }

        if (filled($user->country_id)) {
            return (int) $listing->country_id === (int) $user->country_id;
        }

        return true;
    }
}
