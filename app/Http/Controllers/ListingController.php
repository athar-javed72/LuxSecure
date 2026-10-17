<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\TypeAttribute;
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

        $countryId = $request->integer('country_id') ?: \App\Support\CountryContext::current()?->id;
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
                $query->where("attributes->{$attr->key}", (float) $value);
            } else {
                $query->where("attributes->{$attr->key}", $value);
            }
        }

        $listings = match ($request->get('sort')) {
            'price_asc' => $query->orderBy('price')->paginate(12)->withQueryString(),
            'price_desc' => $query->orderByDesc('price')->paginate(12)->withQueryString(),
            default => $query->latest()->paginate(12)->withQueryString(),
        };

        return view('properties', [
            'properties' => $listings,
            'listings' => $listings,
            'listingTypes' => ListingType::query()->where('is_active', true)->orderBy('name')->get(),
            'cities' => City::query()->where('is_active', true)->orderBy('name')->get(),
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
