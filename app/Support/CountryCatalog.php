<?php

namespace App\Support;

use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CountryCatalog
{
    public const CACHE_KEY = 'countries.active.v1';

    public static function active(): Collection
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), function () {
            return Country::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function isActive(int $countryId): bool
    {
        return self::active()->contains(fn (Country $c) => (int) $c->id === $countryId);
    }

    /** Active countries that accept new records (excludes wind-down). */
    public static function selectable(): Collection
    {
        return self::active()->filter(fn (Country $c) => ! $c->wind_down)->values();
    }

    public static function forUser(?User $user = null): Collection
    {
        $list = self::selectable();
        if ($user && $user->isCountryScoped() && filled($user->country_id)) {
            return $list->filter(fn (Country $c) => (int) $c->id === (int) $user->country_id)->values();
        }

        return $list;
    }
}
