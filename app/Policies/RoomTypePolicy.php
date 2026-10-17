<?php

namespace App\Policies;

use App\Models\RoomType;
use App\Models\User;

class RoomTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('listings.view.any') || $user->can('listings.view.own');
    }

    public function view(User $user, RoomType $roomType): bool
    {
        if ($user->can('listings.view.any')) {
            return true;
        }

        return $user->can('listings.view.own') && $user->ownsListing($roomType->listing);
    }

    public function create(User $user): bool
    {
        return $user->can('listings.create') || $user->can('listings.update.own') || $user->can('listings.update.any');
    }

    public function update(User $user, RoomType $roomType): bool
    {
        if ($user->can('listings.update.any')) {
            return true;
        }

        return $user->can('listings.update.own') && $user->ownsListing($roomType->listing);
    }

    public function delete(User $user, RoomType $roomType): bool
    {
        if ($user->can('listings.delete.any')) {
            return true;
        }

        return $user->can('listings.delete.own') && $user->ownsListing($roomType->listing);
    }
}
