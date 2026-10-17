<?php

namespace App\Support\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait CountryScoped
{
    public function scopeForUserCountry(Builder $query, ?User $user): Builder
    {
        if (! $user || $user->isSuperAdmin()) {
            return $query;
        }

        if ($user->hasRole('admin') && blank($user->country_id)) {
            return $query;
        }

        if (filled($user->country_id)) {
            return $query->where($query->getModel()->getTable().'.country_id', $user->country_id);
        }

        return $query;
    }
}
