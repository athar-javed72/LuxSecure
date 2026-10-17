<?php

namespace App\Policies;

use App\Models\RentPayment;
use App\Models\User;

class RentPaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('payments.view.any') || $user->can('payments.view.own');
    }

    public function view(User $user, RentPayment $payment): bool
    {
        if ($user->can('payments.view.any')) {
            return true;
        }

        if (! $user->can('payments.view.own')) {
            return false;
        }

        $tenancy = $payment->tenancy;

        return (int) $tenancy->landlord_id === (int) $user->id
            || (int) $tenancy->tenant_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('payments.create');
    }

    public function update(User $user, RentPayment $payment): bool
    {
        if ($user->can('payments.update.any')) {
            return true;
        }

        return $user->can('payments.update.own')
            && (int) $payment->tenancy->landlord_id === (int) $user->id;
    }

    public function delete(User $user, RentPayment $payment): bool
    {
        if ($user->can('payments.delete.any')) {
            return true;
        }

        return $user->can('payments.delete.own')
            && (int) $payment->tenancy->landlord_id === (int) $user->id;
    }
}
