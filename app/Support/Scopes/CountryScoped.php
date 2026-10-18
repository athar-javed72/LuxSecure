<?php

namespace App\Support\Scopes;

use App\Models\User;
use App\Support\Authorization\CountryAccess;
use Illuminate\Database\Eloquent\Builder;

trait CountryScoped
{
    public function scopeForUserCountry(Builder $query, ?User $user): Builder
    {
        return CountryAccess::constrain($query, $user);
    }
}
