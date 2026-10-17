<?php

namespace App\Policies;

use App\Models\AgentProfile;
use App\Models\User;

class AgentProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('agents.view') || $user->can('agents.manage');
    }

    public function view(User $user, AgentProfile $profile): bool
    {
        return $user->can('agents.view')
            || $user->can('agents.manage')
            || (int) $profile->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('agents.manage');
    }

    public function update(User $user, AgentProfile $profile): bool
    {
        return $user->can('agents.manage')
            || ($user->can('agents.update') && (int) $profile->user_id === (int) $user->id);
    }

    public function delete(User $user, AgentProfile $profile): bool
    {
        return $user->can('agents.manage');
    }
}
