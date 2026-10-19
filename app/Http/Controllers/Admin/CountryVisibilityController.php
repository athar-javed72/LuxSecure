<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Support\Authorization\CountryAccess;
use App\Support\CountryReadiness;
use App\Support\CountryVisibility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CountryVisibilityController extends Controller
{
    public function edit(Request $request): View
    {
        Gate::authorize('countries.manage_visibility');
        abort_unless(CountryAccess::allowsGlobal($request->user()), 403);

        $search = trim((string) $request->query('q', ''));

        $countries = Country::query()
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->where(function ($qq) use ($like, $search) {
                    $qq->where('name', 'like', $like)
                        ->orWhere('iso2', 'like', $like)
                        ->orWhere('iso3', 'like', $like);
                    if (ctype_digit($search)) {
                        $qq->orWhere('id', (int) $search);
                    }
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (Country $country) {
                $readiness = CountryReadiness::check($country);
                $blockers = CountryVisibility::disableBlockers($country);

                return [
                    'model' => $country,
                    'readiness' => $readiness,
                    'blockers' => $blockers,
                ];
            });

        return view('admin.countries.visibility', [
            'rows' => $countries,
            'search' => $search,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('countries.manage_visibility');
        abort_unless(CountryAccess::allowsGlobal($request->user()), 403);

        $data = $request->validate([
            'enabled' => ['nullable', 'array'],
            'enabled.*' => ['integer', 'exists:countries,id'],
            'confirm_wind_down' => ['sometimes', 'boolean'],
        ]);

        $result = CountryVisibility::sync(
            $data['enabled'] ?? [],
            $request->boolean('confirm_wind_down')
        );

        if ($result['errors'] !== []) {
            return back()
                ->withInput()
                ->withErrors(['enabled' => array_values($result['errors'])])
                ->with('error', 'Some countries could not be updated. Fix readiness or confirm wind-down.');
        }

        $parts = [];
        if ($result['activated'] !== []) {
            $parts[] = count($result['activated']).' activated';
        }
        if ($result['deactivated'] !== []) {
            $parts[] = count($result['deactivated']).' deactivated';
        }
        if ($result['wind_down'] !== []) {
            $parts[] = count($result['wind_down']).' wind-down';
        }

        return redirect()
            ->route('admin.countries.visibility')
            ->with('success', $parts === [] ? 'No changes.' : 'Saved: '.implode(', ', $parts).'.');
    }
}
