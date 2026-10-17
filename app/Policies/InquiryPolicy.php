<?php

namespace App\Policies;

use App\Models\ContactInquiry;
use App\Models\User;

class InquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('inquiries.view.any') || $user->can('inquiries.view.own');
    }

    public function view(User $user, ContactInquiry $inquiry): bool
    {
        if ($user->can('inquiries.view.any')) {
            return true;
        }

        return $user->can('inquiries.view.own') && $this->owns($user, $inquiry);
    }

    public function update(User $user, ContactInquiry $inquiry): bool
    {
        if ($user->can('inquiries.update.any')) {
            return true;
        }

        return $user->can('inquiries.update.own') && $this->owns($user, $inquiry);
    }

    public function delete(User $user, ContactInquiry $inquiry): bool
    {
        if ($user->can('inquiries.delete.any')) {
            return true;
        }

        return $user->can('inquiries.delete.own') && $this->owns($user, $inquiry);
    }

    private function owns(User $user, ContactInquiry $inquiry): bool
    {
        $inquiry->loadMissing('listing');

        if (! $inquiry->listing) {
            return false;
        }

        return $user->ownsListing($inquiry->listing);
    }
}
