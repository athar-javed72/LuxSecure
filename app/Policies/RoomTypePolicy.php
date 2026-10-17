<?php

namespace App\Policies;

use App\Models\RoomType;
use App\Models\User;

class RoomTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('rooms.view.any') || $user->can('rooms.view.own');
    }

    public function view(User $user, RoomType $roomType): bool
    {
        if ($user->can('rooms.view.any')) {
            return true;
        }

        return $user->can('rooms.view.own') && $user->ownsListing($roomType->listing);
    }

    public function create(User $user): bool
    {
        return $user->can('rooms.create') || $user->can('rooms.update.own') || $user->can('rooms.update.any');
    }

    public function update(User $user, RoomType $roomType): bool
    {
        if ($user->can('rooms.update.any')) {
            return true;
        }

        return $user->can('rooms.update.own') && $user->ownsListing($roomType->listing);
    }

    public function delete(User $user, RoomType $roomType): bool
    {
        if ($user->can('rooms.delete.any')) {
            return true;
        }

        return $user->can('rooms.delete.own') && $user->ownsListing($roomType->listing);
    }
}
