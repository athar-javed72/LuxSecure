<?php

namespace App\Support;

use App\Models\Country;
use App\Models\CountrySetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class CountryContext
{
    public static function current(): ?Country
    {
        $id = session('country_id') ?: Auth::user()?->country_id;
        if ($id) {
            return Country::query()->where('is_active', true)->find($id);
        }

        return Country::query()->where('iso2', 'PK')->where('is_active', true)->first()
            ?? Country::query()->where('is_active', true)->orderBy('sort_order')->first();
    }

    public static function set(?int $countryId, ?string $currency = null, ?string $locale = null): void
    {
        $country = $countryId
            ? Country::query()->where('is_active', true)->find($countryId)
            : null;

        if ($country) {
            session([
                'country_id' => $country->id,
                'currency' => $currency ?: $country->currency_code,
                'locale' => $locale ?: $country->default_locale,
            ]);
            app()->setLocale(session('locale', 'en'));
        }
    }

    public static function setting(string $key, mixed $default = null): mixed
    {
        $country = self::current();
        if ($country) {
            $value = CountrySetting::getValue($country->id, $key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return Setting::getValue($key, $default);
    }

    public static function contact(string $key): ?string
    {
        $value = self::setting($key);
        if ($value === null || $value === '' || str_starts_with((string) $value, '[PLACEHOLDER')) {
            return null;
        }

        return (string) $value;
    }

    /**
     * Commission percent for a listing purpose (sale|rent|short_stay).
     */
    public static function commissionForPurpose(?string $purpose): float
    {
        $key = match ($purpose) {
            'rent' => 'commerce.commission_rent',
            'sale' => 'commerce.commission_sale',
            'short_stay' => 'commerce.commission_hotel',
            default => 'commerce.commission_rate',
        };

        return (float) self::setting($key, Setting::getValue($key, Setting::getValue('commerce.commission_rate', 0)));
    }
}
