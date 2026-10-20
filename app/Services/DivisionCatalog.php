<?php

namespace App\Services;

use App\Models\City;
use App\Models\Faq;
use App\Models\Listing;
use App\Models\Review;
use App\Support\StorageDisk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DivisionCatalog
{
    public function config(string $key): array
    {
        $cfg = config("divisions.{$key}");
        if (! is_array($cfg)) {
            abort(404);
        }

        return $cfg;
    }

    public function listingQuery(array $division): Builder
    {
        $q = Listing::query()->published()->with(['images', 'listingType', 'city']);
        $scope = $division['scope'] ?? [];

        if (! empty($scope['purpose'])) {
            $q->where('purpose', $scope['purpose']);
        }

        if (! empty($scope['type_slugs'])) {
            $q->whereHas('listingType', fn ($t) => $t->whereIn('slug', $scope['type_slugs']));
        }

        // Living: purpose=rent already; optional attribute boost is soft (include all rent).
        return $q;
    }

    public function stats(string $divisionKey): array
    {
        return Cache::remember("division.stats.{$divisionKey}", 600, function () use ($divisionKey) {
            $division = $this->config($divisionKey);
            $base = $this->listingQuery($division);

            $listings = (clone $base)->count();
            $cities = (clone $base)->whereNotNull('city_id')->distinct('city_id')->count('city_id');
            $countries = (clone $base)->whereNotNull('country_id')->distinct('country_id')->count('country_id');

            $items = [];
            if ($listings > 0) {
                $items[] = ['label' => ucfirst($division['listing_label'] ?? 'listings'), 'value' => $listings];
            }
            if ($cities > 0) {
                $items[] = ['label' => 'Cities', 'value' => $cities];
            }
            if ($countries > 0) {
                $items[] = ['label' => 'Countries', 'value' => $countries];
            }

            return $items;
        });
    }

    public function featured(string $divisionKey, int $limit = 8): Collection
    {
        return $this->listingQuery($this->config($divisionKey))
            ->latest()
            ->take($limit)
            ->get();
    }

    public function destinations(string $divisionKey, int $limit = 8): Collection
    {
        $division = $this->config($divisionKey);
        $purpose = $division['scope']['purpose'] ?? null;
        $label = $division['listing_label'] ?? 'listings';

        $rows = Listing::query()
            ->published()
            ->when($purpose, fn ($q) => $q->where('purpose', $purpose))
            ->whereNotNull('city_id')
            ->select('city_id', DB::raw('COUNT(*) as listings_count'))
            ->groupBy('city_id')
            ->orderByDesc('listings_count')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $cities = City::query()
            ->with('country')
            ->whereIn('id', $rows->pluck('city_id'))
            ->get()
            ->keyBy('id');

        $fallbackCovers = Listing::query()
            ->published()
            ->whereIn('city_id', $rows->pluck('city_id'))
            ->when($purpose, fn ($q) => $q->where('purpose', $purpose))
            ->with('images')
            ->latest()
            ->get()
            ->groupBy('city_id');

        return $rows->map(function ($row) use ($cities, $label, $purpose, $fallbackCovers) {
            $city = $cities->get($row->city_id);
            if (! $city) {
                return null;
            }
            $cover = $city->cover_path
                ? StorageDisk::url($city->cover_path)
                : null;
            if (! $cover) {
                $listing = optional($fallbackCovers->get($row->city_id))->first();
                $cover = $listing?->featured_image
                    ? StorageDisk::url($listing->featured_image)
                    : ($listing?->images->first() ? StorageDisk::url($listing->images->first()->path) : null);
            }

            return [
                'city' => $city,
                'count' => (int) $row->listings_count,
                'label' => $row->listings_count.' '.($row->listings_count === 1 ? rtrim($label, 's') : $label),
                'cover' => $cover,
                'focal' => ($city->cover_focal_x ?? 50).'% '.($city->cover_focal_y ?? 50).'%',
                'href' => route('properties', array_filter([
                    'purpose' => $purpose,
                    'city_id' => $city->id,
                ])),
            ];
        })->filter()->values();
    }

    public function amenities(string $divisionKey, int $limit = 6): Collection
    {
        $cacheKey = "division.amenities.{$divisionKey}";
        $cached = Cache::get($cacheKey);
        if (! is_array($cached)) {
            $division = $this->config($divisionKey);
            $keys = $division['amenity_keys'] ?? [];
            $cached = [];
            if ($keys !== []) {
                $listings = $this->listingQuery($division)->limit(200)->get(['id', 'attributes']);
                $counts = [];
                foreach ($listings as $listing) {
                    $attrs = is_array($listing->attributes) ? $listing->attributes : [];
                    foreach ($keys as $key) {
                        $val = $attrs[$key] ?? null;
                        if ($val === true || $val === 1 || $val === '1' || (is_string($val) && $val !== '' && $val !== '0')) {
                            $counts[$key] = ($counts[$key] ?? 0) + 1;
                        }
                    }
                }

                arsort($counts);
                $top = collect($counts)->take($limit);
                if ($top->count() >= 3) {
                    $icons = config('division_icons.amenities', []);
                    $cached = $top->map(function ($count, $key) use ($division, $icons) {
                        return [
                            'key' => $key,
                            'label' => str_replace('_', ' ', ucfirst($key)),
                            'count' => $count,
                            'icon' => $icons[$key] ?? ($icons['default'] ?? ''),
                            'href' => route('properties', array_filter([
                                'purpose' => $division['scope']['purpose'] ?? null,
                                $key => 1,
                            ])),
                        ];
                    })->values()->all();
                }
            }
            Cache::put($cacheKey, $cached, 600);
        }

        return collect($cached);
    }

    public function faqs(string $divisionKey): Collection
    {
        $tag = $this->config($divisionKey)['faq_tag'] ?? null;

        return Faq::published()
            ->when($tag, fn ($q) => $q->where(function ($inner) use ($tag) {
                $inner->where('tag', $tag)->orWhereNull('tag')->orWhere('tag', '');
            }))
            ->take(12)
            ->get();
    }

    public function reviews(string $divisionKey, int $limit = 6): Collection
    {
        $division = $this->config($divisionKey);
        $listingIds = $this->listingQuery($division)->limit(200)->pluck('id');
        if ($listingIds->isEmpty()) {
            return collect();
        }

        return Review::approved()
            ->whereIn('listing_id', $listingIds)
            ->with(['user', 'listing'])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function browseHref(array $division): string
    {
        return route('properties', array_filter([
            'purpose' => $division['scope']['purpose'] ?? null,
        ]));
    }
}
