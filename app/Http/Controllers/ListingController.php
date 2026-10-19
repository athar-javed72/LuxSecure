<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\TypeAttribute;
use App\Support\CountryContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Listing::query()
            ->published()
            ->with(['images', 'listingType', 'city']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });
        }

        if ($request->filled('type')) {
            $type = $request->string('type')->toString();
            $query->where(function ($q) use ($type) {
                if (ctype_digit($type)) {
                    $q->where('listing_type_id', (int) $type);
                } else {
                    $q->whereHas('listingType', fn ($t) => $t->where('slug', $type));
                }
            });
        }

        if ($request->filled('purpose') && in_array($request->purpose, Listing::PURPOSES, true)) {
            $query->where('purpose', $request->purpose);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }

        // More-filters Country: Pakistan → PKR, any other country → USD (display currency).
        if ($request->filled('country_id')) {
            $selectedCountry = Country::query()
                ->where('is_active', true)
                ->find($request->integer('country_id'));
            if ($selectedCountry) {
                $displayCurrency = strtoupper((string) $selectedCountry->iso2) === 'PK' ? 'PKR' : 'USD';
                Currency::query()->firstOrCreate(
                    ['code' => $displayCurrency],
                    [
                        'symbol' => $displayCurrency === 'PKR' ? 'Rs' : '$',
                        'decimals' => $displayCurrency === 'PKR' ? 0 : 2,
                        'is_active' => true,
                    ]
                );
                CountryContext::set($selectedCountry->id, $displayCurrency);
            }
        }

        $countryId = $request->integer('country_id') ?: CountryContext::current()?->id;
        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        if ($request->filled('price')) {
            $range = $request->price;
            if (preg_match('/^(\d+)-(\d+)$/', $range, $m)) {
                $query->whereBetween('price', [(int) $m[1], (int) $m[2]]);
            }
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }

        $filterableAttrs = TypeAttribute::query()
            ->where('is_filterable', true)
            ->when($request->filled('type'), function ($q) use ($request) {
                $type = $request->string('type')->toString();
                if (ctype_digit($type)) {
                    $q->where('listing_type_id', (int) $type);
                } else {
                    $q->whereHas('listingType', fn ($t) => $t->where('slug', $type));
                }
            })
            ->orderBy('sort_order')
            ->get();

        foreach ($filterableAttrs as $attr) {
            $param = 'attr_'.$attr->key;
            if (! $request->filled($param)) {
                continue;
            }
            $value = $request->input($param);
            if ($attr->input_type === 'boolean') {
                $query->where("attributes->{$attr->key}", filter_var($value, FILTER_VALIDATE_BOOLEAN) ? true : false);
            } elseif ($attr->input_type === 'number') {
                $num = (float) $value;
                // SQLite JSON stores whole numbers as ints; float compare misses them.
                $query->where(
                    "attributes->{$attr->key}",
                    fmod($num, 1.0) === 0.0 ? (int) $num : $num
                );
            } else {
                $query->where("attributes->{$attr->key}", $value);
            }
        }

        $listings = match ($request->get('sort')) {
            'price_asc' => $query->orderBy('price')->paginate(12)->withQueryString(),
            'price_desc' => $query->orderByDesc('price')->paginate(12)->withQueryString(),
            default => $query->latest()->paginate(12)->withQueryString(),
        };

        $citiesQuery = City::query()->where('is_active', true)->orderBy('name');
        if ($request->filled('country_id')) {
            $citiesQuery->where('country_id', $request->integer('country_id'));
        }

        return view('properties', [
            'properties' => $listings,
            'listings' => $listings,
            'listingTypes' => ListingType::query()->where('is_active', true)->orderBy('name')->get(),
            'cities' => $citiesQuery->get(),
            'filterCountries' => Country::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'filterableAttributes' => $filterableAttrs,
        ]);
    }

    public function show(Listing $listing): View
    {
        if ($listing->status !== 'published') {
            abort(404);
        }
        $listing->load(['images', 'listingType', 'city', 'area', 'roomTypes', 'agent.agentProfile']);
        $reviews = $listing->approvedReviews()->with('user')->latest()->get();

        return view('properties.show', [
            'property' => $listing,
            'listing' => $listing,
            'reviews' => $reviews,
            'averageRating' => $listing->averageRating(),
            'agentAverageRating' => $listing->agent?->agentProfile?->averageRating(),
        ]);
    }
}
