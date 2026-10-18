<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use App\Support\CountryContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'currency' => ['nullable', 'string', 'size:3', 'exists:currencies,code'],
            'locale' => ['nullable', 'string', 'max:12', 'exists:languages,code'],
        ]);

        $user = $request->user();

        // Panel country-scoped roles may not switch their assigned country via the storefront switcher.
        if ($user?->isCountryScoped()) {
            $country = Country::query()
                ->where('is_active', true)
                ->find($user->country_id);
        } else {
            $country = isset($data['country_id'])
                ? Country::query()->where('is_active', true)->find($data['country_id'])
                : CountryContext::current();
        }

        $currency = $data['currency']
            ?? $country?->currency_code
            ?? Currency::query()->where('is_active', true)->value('code');

        if ($currency && ! Currency::query()->where('code', $currency)->where('is_active', true)->exists()) {
            $currency = $country?->currency_code;
        }

        $locale = $data['locale']
            ?? $country?->default_locale
            ?? Language::query()->where('is_active', true)->value('code')
            ?? 'en';

        if ($locale && ! Language::query()->where('code', $locale)->where('is_active', true)->exists()) {
            $locale = $country?->default_locale ?? 'en';
        }

        $locales = $country?->locales;
        if (is_array($locales) && $locales !== [] && ! in_array($locale, $locales, true)) {
            $locale = $country->default_locale ?: ($locales[0] ?? 'en');
        }

        CountryContext::set($country?->id, $currency, $locale);

        if ($user && ! $user->isCountryScoped() && ! $user->isStaff()) {
            $user->forceFill([
                'preferred_currency' => $currency,
                'preferred_locale' => $locale,
            ])->save();
        } elseif ($user && ! $user->isCountryScoped() && $user->isStaff() && ! $user->hasRole('country_admin')) {
            // Global staff: session prefs only (do not rewrite country_id).
            $user->forceFill([
                'preferred_currency' => $currency,
                'preferred_locale' => $locale,
            ])->save();
        }

        return back();
    }
}
