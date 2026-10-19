<?php

namespace App\Rules;

use App\Models\Country;
use App\Support\CountryCatalog;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveCountry implements ValidationRule
{
    public function __construct(private readonly ?int $existingCountryId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $id = (int) $value;
        $country = Country::query()->find($id);
        if (! $country) {
            $fail('The selected country is invalid.');

            return;
        }

        // Editing an existing record may keep a now-disabled / wind-down country.
        if ($this->existingCountryId !== null && $id === (int) $this->existingCountryId) {
            return;
        }

        if (! $country->is_active || $country->wind_down) {
            $fail('The selected country is not available.');

            return;
        }

        if (! CountryCatalog::isActive($id)) {
            $fail('The selected country is not available.');
        }
    }
}
