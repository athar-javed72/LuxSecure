<?php

namespace App\Policies;

use App\Models\ListingType;
use App\Models\User;

class ListingTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ListingType $listingType): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ListingType $listingType): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ListingType $listingType): bool
    {
        return $user->isAdmin();
    }
}
