<?php

namespace App\Support;

use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CountryCatalog
{
    public const CACHE_KEY = 'countries.active.v2';

    public static function active(): Collection
    {
        $cached = Cache::get(self::CACHE_KEY);

        if (self::isValidCountryCollection($cached)) {
            return $cached;
        }

        if ($cached !== null) {
            Cache::forget(self::CACHE_KEY);
        }

        // Cache ids only — Eloquent Collection unserialize can yield __PHP_Incomplete_Class.
        $ids = Cache::remember(self::CACHE_KEY, now()->addHour(), function () {
            return Country::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('id')
                ->all();
        });

        if (! is_array($ids) || $ids === []) {
            Cache::forget(self::CACHE_KEY);

            return collect();
        }

        $byId = Country::query()->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)
            ->map(fn ($id) => $byId->get($id))
            ->filter()
            ->values();
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('countries.active.v1');
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

    private static function isValidCountryCollection(mixed $value): bool
    {
        if (! $value instanceof Collection) {
            return false;
        }

        foreach ($value as $item) {
            if (! $item instanceof Country) {
                return false;
            }
        }

        return true;
    }
}
