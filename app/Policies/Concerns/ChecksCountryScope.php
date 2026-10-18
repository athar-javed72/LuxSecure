<?php

namespace App\Policies\Concerns;

use App\Models\User;
use App\Support\Authorization\CountryAccess;

trait ChecksCountryScope
{
    protected function inCountryScope(User $user, mixed $modelOrCountryId): bool
    {
        $countryId = is_object($modelOrCountryId)
            ? ($modelOrCountryId->country_id ?? null)
            : $modelOrCountryId;

        return CountryAccess::matches($user, $countryId);
    }
}
