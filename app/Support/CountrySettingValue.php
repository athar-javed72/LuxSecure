<?php

namespace App\Support;

use App\Models\CountrySetting;
use Illuminate\Support\Facades\Schema;

class CountrySettingValue
{
    public static function get(int $countryId, string $key, mixed $default = null): mixed
    {
        if (! Schema::hasTable('country_settings')) {
            return $default;
        }

        $row = CountrySetting::query()
            ->where('country_id', $countryId)
            ->where('key', $key)
            ->first();

        return $row?->value ?? $default;
    }
}
