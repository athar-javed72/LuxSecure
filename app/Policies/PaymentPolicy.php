<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('payments.view.any') || $user->can('payments.view.own');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->can('payments.view.any')) {
            return true;
        }

        return $user->can('payments.view.own')
            && (int) $payment->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('payments.create');
    }

    public function update(User $user, Payment $payment): bool
    {
        if ($user->can('payments.update.any')) {
            return true;
        }

        return $user->can('payments.update.own')
            && (int) $payment->user_id === (int) $user->id;
    }

    public function delete(User $user, Payment $payment): bool
    {
        if ($user->can('payments.delete.any')) {
            return true;
        }

        return $user->can('payments.delete.own')
            && (int) $payment->user_id === (int) $user->id;
    }

    public function approve(User $user, Payment $payment): bool
    {
        return $user->can('payments.update.any');
    }
}
