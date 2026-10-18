<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Policies\Concerns\ChecksCountryScope;

class ReviewPolicy
{
    use ChecksCountryScope;

    public function viewAny(User $user): bool
    {
        return $user->can('reviews.view.any') || $user->can('reviews.moderate');
    }

    public function view(User $user, Review $review): bool
    {
        if (($user->can('reviews.view.any') || $user->can('reviews.moderate'))
            && $this->reviewInCountry($user, $review)) {
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
        return $user->can('reviews.moderate') && $this->reviewInCountry($user, $review);
    }

    public function delete(User $user, Review $review): bool
    {
        return ($user->can('reviews.moderate') || $user->can('reviews.delete.any'))
            && $this->reviewInCountry($user, $review);
    }

    public function moderate(User $user): bool
    {
        return $user->can('reviews.moderate');
    }

    private function reviewInCountry(User $user, Review $review): bool
    {
        $review->loadMissing('listing');

        return $this->inCountryScope($user, $review->listing?->country_id ?? $review->country_id ?? null);
    }
}
