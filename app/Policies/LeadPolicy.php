<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('leads.view.any') || $user->can('leads.view.own');
    }

    public function view(User $user, Lead $lead): bool
    {
        if ($user->can('leads.view.any')) {
            return true;
        }

        return $user->can('leads.view.own') && (int) $lead->assigned_agent_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('leads.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        if ($user->can('leads.update.any')) {
            return true;
        }

        return $user->can('leads.update.own') && (int) $lead->assigned_agent_id === (int) $user->id;
    }

    public function delete(User $user, Lead $lead): bool
    {
        if ($user->can('leads.delete.any')) {
            return true;
        }

        return $user->can('leads.delete.own') && (int) $lead->assigned_agent_id === (int) $user->id;
    }
}
