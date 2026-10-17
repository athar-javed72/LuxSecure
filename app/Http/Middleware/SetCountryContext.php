<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Support\CountryContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCountryContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('country_id')) {
            $user = $request->user();
            CountryContext::set(
                $user?->country_id,
                $user?->preferred_currency,
                $user?->preferred_locale
            );
        } else {
            app()->setLocale(session('locale', config('app.locale', 'en')));
        }

        $locale = app()->getLocale();
        $isRtl = Language::query()->where('code', $locale)->value('is_rtl') ?? false;
        view()->share('isRtl', (bool) $isRtl);
        view()->share('activeCountry', CountryContext::current());

        return $next($request);
    }
}
