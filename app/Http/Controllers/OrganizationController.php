<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function show(Request $request, Organization $organization): View
    {
        abort_unless($organization->is_active, 404);

        $branches = $organization->branches()
            ->published()
            ->with(['city', 'images'])
            ->when($request->filled('country_id'), fn ($q) => $q->where('country_id', $request->integer('country_id')))
            ->when($request->filled('city_id'), fn ($q) => $q->where('city_id', $request->integer('city_id')))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        $cities = City::query()
            ->where('is_active', true)
            ->when(
                $request->filled('country_id'),
                fn ($q) => $q->where('country_id', $request->integer('country_id')),
                fn ($q) => $q->when($organization->country_id, fn ($qq) => $qq->where('country_id', $organization->country_id))
            )
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('organizations.show', [
            'organization' => $organization->loadMissing('country'),
            'branches' => $branches,
            'cities' => $cities,
        ]);
    }
}
