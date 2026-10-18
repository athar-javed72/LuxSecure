<?php

namespace App\Http\Middleware;

use App\Models\Country;
use App\Models\Language;
use App\Support\CountryContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetCountryContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isCountryScoped() && filled($user->country_id)) {
            // Lock country-scoped panel users to their assigned country.
            CountryContext::set(
                (int) $user->country_id,
                $user->preferred_currency,
                $user->preferred_locale
            );
        } elseif (! session()->has('country_id')) {
            CountryContext::set(
                $user?->country_id ? (int) $user->country_id : null,
                $user?->preferred_currency,
                $user?->preferred_locale
            );
        } else {
            $country = Country::query()
                ->where('is_active', true)
                ->find(session('country_id'));

            if (! $country) {
                CountryContext::set(
                    $user?->country_id ? (int) $user->country_id : null,
                    $user?->preferred_currency,
                    $user?->preferred_locale
                );
            } else {
                app()->setLocale(session('locale', $country->default_locale ?: config('app.locale', 'en')));
            }
        }

        $locale = app()->getLocale();
        $isRtl = false;

        if (Schema::hasTable('languages')) {
            if (! Language::query()->where('code', $locale)->where('is_active', true)->exists()) {
                $locale = config('app.locale', 'en');
                app()->setLocale($locale);
            }
            $isRtl = (bool) (Language::query()->where('code', $locale)->value('is_rtl') ?? false);
        }

        view()->share('isRtl', $isRtl);
        view()->share('activeCountry', CountryContext::current());

        return $next($request);
    }
}
