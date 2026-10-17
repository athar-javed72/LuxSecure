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
        if ($user->can('listings.view.any')) {
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
        if ($user->can('listings.update.any')) {
            return true;
        }

        return $user->can('listings.update.own') && $user->ownsListing($listing);
    }

    public function delete(User $user, Listing $listing): bool
    {
        if ($user->can('listings.delete.any')) {
            return true;
        }

        return $user->can('listings.delete.own') && $user->ownsListing($listing);
    }
}
