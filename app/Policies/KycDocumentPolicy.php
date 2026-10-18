<?php

namespace App\Policies;

use App\Models\KycDocument;
use App\Models\User;
use App\Policies\Concerns\ChecksCountryScope;

class KycDocumentPolicy
{
    use ChecksCountryScope;

    public function viewAny(User $user): bool
    {
        return $user->can('kyc.view.any') || $user->can('kyc.review') || $user->can('kyc.view.own');
    }

    public function view(User $user, KycDocument $document): bool
    {
        if (($user->can('kyc.view.any') || $user->can('kyc.review'))
            && $this->inCountryScope($user, $document->user)) {
            return true;
        }

        return $user->can('kyc.view.own')
            && (int) $document->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('kyc.submit') || $user->can('kyc.create');
    }

    public function review(User $user, KycDocument $document): bool
    {
        return $user->can('kyc.review') && $this->inCountryScope($user, $document->user);
    }
}
