<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Support\Authorization\CountryAccess;
use App\Support\CountryCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CitySearchController extends Controller
{
    public function __invoke(Request $request, Country $country): JsonResponse
    {
        $user = $request->user();
        if ($user && ! CountryAccess::matches($user, $country->id) && ! CountryAccess::allowsGlobal($user)) {
            abort(403);
        }

        // Public / unauthenticated: only active non-wind-down countries.
        if (! $user || ! $user->can('panel.access')) {
            if (! $country->is_active || $country->wind_down || ! CountryCatalog::isActive((int) $country->id)) {
                return response()->json(['data' => [], 'meta' => ['total' => 0, 'per_page' => 50]]);
            }
        }

        $search = trim((string) $request->query('search', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 50;

        $query = City::query()
            ->where('country_id', $country->id)
            ->where('is_active', true);

        if (mb_strlen($search) >= 2) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('state_name', 'like', $like)
                    ->orWhere('state_code', 'like', $like);
            })->orderBy('name');
        } else {
            // Empty / short search: top by population then name.
            $query->orderByRaw('population IS NULL')
                ->orderByDesc('population')
                ->orderBy('name');
        }

        $paginator = $query->paginate($perPage, ['id', 'name', 'state_name', 'state_code', 'country_id', 'population'], 'page', $page);

        // Always include a specifically requested selected city (edit forms).
        $selectedId = $request->integer('selected_id') ?: null;
        $items = collect($paginator->items())->map(fn (City $c) => $this->serialize($c));

        if ($selectedId) {
            $selected = City::query()
                ->where('country_id', $country->id)
                ->where('id', $selectedId)
                ->first();
            if ($selected && ! $items->contains(fn ($row) => (int) $row['id'] === (int) $selected->id)) {
                $items = $items->prepend($this->serialize($selected))->take($perPage)->values();
            }
        }

        return response()->json([
            'data' => $items->values(),
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $perPage,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function serialize(City $city): array
    {
        return [
            'id' => $city->id,
            'name' => $city->name,
            'state_name' => $city->state_name,
            'state_code' => $city->state_code,
            'label' => $city->displayLabel(),
            'country_id' => $city->country_id,
        ];
    }
}
