<?php

namespace App\Rules;

use App\Models\City;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CityBelongsToCountry implements ValidationRule
{
    public function __construct(private readonly mixed $countryId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $city = City::query()->find((int) $value);
        if (! $city) {
            $fail('The selected city is invalid.');

            return;
        }

        $countryId = $this->countryId;
        if ($countryId === null || $countryId === '') {
            $fail('Select a country before choosing a city.');

            return;
        }

        if ((int) $city->country_id !== (int) $countryId) {
            $fail('The selected city does not belong to the selected country.');
        }
    }
}
