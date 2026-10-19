<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\CitySearchController;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Support\Authorization\CountryAccess;
use App\Support\CountryCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum') ?? $request->user();
        $countries = CountryCatalog::selectable()
            ->filter(function (Country $c) use ($user) {
                if ($user && $user->isCountryScoped()) {
                    return CountryAccess::matches($user, $c->id);
                }

                return true;
            })
            ->values()
            ->map(fn (Country $c) => [
                'id' => $c->id,
                'iso2' => $c->iso2,
                'iso3' => $c->iso3,
                'name' => $c->name,
                'currency_code' => $c->currency_code,
                'default_locale' => $c->default_locale,
            ]);

        return response()->json([
            'success' => true,
            'data' => $countries,
        ]);
    }

    public function cities(Request $request, Country $country): JsonResponse
    {
        return app(CitySearchController::class)($request, $country);
    }
}
