<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('reviews.view.any') || $user->can('reviews.moderate');
    }

    public function view(User $user, Review $review): bool
    {
        if ($user->can('reviews.view.any') || $user->can('reviews.moderate')) {
            return true;
        }

        return $user->can('reviews.view.own')
            && (int) $review->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('reviews.create');
    }

    public function update(User $user, Review $review): bool
    {
        return $user->can('reviews.moderate');
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->can('reviews.moderate') || $user->can('reviews.delete.any');
    }

    public function moderate(User $user): bool
    {
        return $user->can('reviews.moderate');
    }
}
