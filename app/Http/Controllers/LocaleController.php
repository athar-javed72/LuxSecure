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
            'country_id' => ['nullable', 'exists:countries,id'],
            'currency' => ['nullable', 'string', 'size:3', 'exists:currencies,code'],
            'locale' => ['nullable', 'string', 'max:12', 'exists:languages,code'],
        ]);

        $country = isset($data['country_id'])
            ? Country::query()->where('is_active', true)->find($data['country_id'])
            : CountryContext::current();

        $currency = $data['currency']
            ?? $country?->currency_code
            ?? Currency::query()->where('is_active', true)->value('code');

        $locale = $data['locale']
            ?? $country?->default_locale
            ?? Language::query()->where('is_active', true)->value('code')
            ?? 'en';

        CountryContext::set($country?->id, $currency, $locale);

        if ($user = $request->user()) {
            $user->forceFill([
                'country_id' => $country?->id ?? $user->country_id,
                'preferred_currency' => $currency,
                'preferred_locale' => $locale,
            ])->save();
        }

        return back();
    }
}
