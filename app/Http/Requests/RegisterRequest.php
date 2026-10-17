<?php

namespace App\Http\Requests;

use App\Support\CountryContext;
use App\Support\LegalAcceptanceService;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $country = CountryContext::current();
        $phoneRule = ['nullable', 'string', 'max:32'];
        if ($country?->phone_regex) {
            $phoneRule[] = 'regex:/'.$country->phone_regex.'/';
        } else {
            $phoneRule[] = 'regex:/^[0-9+\-\s()]+$/';
        }

        return array_merge([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => $phoneRule,
            'password' => ['required', 'min:8', 'confirmed'],
        ], LegalAcceptanceService::validationRules());
    }
}
