<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Listing $listing): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Listing $listing): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $user->isAdmin();
    }
}
