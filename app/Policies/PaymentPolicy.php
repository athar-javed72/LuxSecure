<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use App\Policies\Concerns\ChecksCountryScope;

class PaymentPolicy
{
    use ChecksCountryScope;

    public function viewAny(User $user): bool
    {
        return $user->can('payments.view.any') || $user->can('payments.view.own');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->can('payments.view.any') && $this->inCountryScope($user, $payment)) {
            return true;
        }

        if ($user->can('payments.view.own') && (int) $payment->user_id === (int) $user->id) {
            return true;
        }

        // Managers: own listing payments.
        if ($user->can('payments.view.own') && $payment->payable) {
            $listing = $payment->payable->listing ?? null;
            if ($listing && $user->ownsListing($listing)) {
                return true;
            }
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('payments.create');
    }

    public function update(User $user, Payment $payment): bool
    {
        if ($user->can('payments.update.any') && $this->inCountryScope($user, $payment)) {
            return true;
        }

        return $user->can('payments.update.own')
            && (int) $payment->user_id === (int) $user->id;
    }

    public function delete(User $user, Payment $payment): bool
    {
        if ($user->can('payments.delete.any') && $this->inCountryScope($user, $payment)) {
            return true;
        }

        return $user->can('payments.delete.own')
            && (int) $payment->user_id === (int) $user->id;
    }

    public function approve(User $user, Payment $payment): bool
    {
        return $user->can('payments.update.any') && $this->inCountryScope($user, $payment);
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->can('payments.refund') && $this->inCountryScope($user, $payment);
    }
}
