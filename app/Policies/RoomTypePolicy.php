<?php

namespace App\Policies;

use App\Models\RoomType;
use App\Models\User;
use App\Policies\Concerns\ChecksCountryScope;

class RoomTypePolicy
{
    use ChecksCountryScope;

    public function viewAny(User $user): bool
    {
        return $user->can('rooms.view.any') || $user->can('rooms.view.own');
    }

    public function view(User $user, RoomType $roomType): bool
    {
        if ($user->can('rooms.view.any') && $this->inCountryScope($user, $roomType->listing)) {
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
        if ($user->can('rooms.update.any') && $this->inCountryScope($user, $roomType->listing)) {
            return true;
        }

        return $user->can('rooms.update.own') && $user->ownsListing($roomType->listing);
    }

    public function delete(User $user, RoomType $roomType): bool
    {
        if ($user->can('rooms.delete.any') && $this->inCountryScope($user, $roomType->listing)) {
            return true;
        }

        return $user->can('rooms.delete.own') && $user->ownsListing($roomType->listing);
    }
}
