<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use App\Policies\Concerns\ChecksCountryScope;

class TicketPolicy
{
    use ChecksCountryScope;

    public function viewAny(User $user): bool
    {
        return $user->can('tickets.view.any') || $user->can('tickets.view.own');
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->can('tickets.view.any') && $this->inCountryScope($user, $ticket->user)) {
            return true;
        }

        return $user->can('tickets.view.own')
            && ((int) $ticket->user_id === (int) $user->id || (int) $ticket->assigned_to === (int) $user->id);
    }

    public function create(User $user): bool
    {
        return $user->can('tickets.create');
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->can('tickets.update.any') && $this->inCountryScope($user, $ticket->user)) {
            return true;
        }

        return $user->can('tickets.update.own')
            && ((int) $ticket->user_id === (int) $user->id || (int) $ticket->assigned_to === (int) $user->id);
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->can('tickets.delete.any') && $this->inCountryScope($user, $ticket->user)) {
            return true;
        }

        return $user->can('tickets.delete.own') && (int) $ticket->user_id === (int) $user->id;
    }
}
