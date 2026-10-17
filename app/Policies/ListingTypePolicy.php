<?php

namespace App\Policies;

use App\Models\ListingType;
use App\Models\User;

class ListingTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('listing_types.view');
    }

    public function view(User $user, ListingType $listingType): bool
    {
        return $user->can('listing_types.view');
    }

    public function create(User $user): bool
    {
        return $user->can('listing_types.create');
    }

    public function update(User $user, ListingType $listingType): bool
    {
        return $user->can('listing_types.update');
    }

    public function delete(User $user, ListingType $listingType): bool
    {
        return $user->can('listing_types.delete');
    }
}
