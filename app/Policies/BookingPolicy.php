<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('bookings.view.any') || $user->can('bookings.view.own');
    }

    public function view(User $user, Booking $booking): bool
    {
        if ($user->can('bookings.view.any')) {
            return true;
        }

        if (! $user->can('bookings.view.own')) {
            return false;
        }

        return (int) $booking->user_id === (int) $user->id
            || $booking->isOwnedByManager($user);
    }

    public function create(User $user): bool
    {
        return $user->can('bookings.create');
    }

    public function update(User $user, Booking $booking): bool
    {
        if ($user->can('bookings.update.any')) {
            return true;
        }

        return $user->can('bookings.update.own')
            && $booking->isOwnedByManager($user);
    }

    public function delete(User $user, Booking $booking): bool
    {
        if ($user->can('bookings.delete.any')) {
            return true;
        }

        return $user->can('bookings.delete.own')
            && $booking->isOwnedByManager($user);
    }

    public function cancelOwn(User $user, Booking $booking): bool
    {
        return (int) $booking->user_id === (int) $user->id
            && in_array($booking->status, ['pending', 'confirmed'], true);
    }
}
