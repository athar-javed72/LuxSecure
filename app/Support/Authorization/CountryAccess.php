<?php

namespace App\Support\Authorization;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class CountryAccess
{
    public static function allowsGlobal(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Platform admin without a country assignment may see all countries.
        return $user->hasRole('admin') && blank($user->country_id);
    }

    public static function requiresCountry(User $user): bool
    {
        return $user->hasRole('country_admin') || (filled($user->country_id) && ! self::allowsGlobal($user));
    }

    public static function matches(?User $user, mixed $countryId): bool
    {
        if (! $user) {
            return false;
        }

        if (self::allowsGlobal($user)) {
            return true;
        }

        if ($user->hasRole('country_admin') && blank($user->country_id)) {
            return false;
        }

        if (filled($user->country_id)) {
            return (int) $countryId === (int) $user->country_id;
        }

        return ! $user->hasRole('country_admin');
    }

    public static function constrain(Builder $query, ?User $user, ?string $column = null): Builder
    {
        if (! $user || self::allowsGlobal($user)) {
            return $query;
        }

        $column ??= $query->getModel()->getTable().'.country_id';

        if ($user->hasRole('country_admin') && blank($user->country_id)) {
            return $query->whereRaw('1 = 0');
        }

        if (filled($user->country_id)) {
            return $query->where($column, $user->country_id);
        }

        if ($user->hasRole('country_admin')) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }
}
