<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $country = \App\Support\CountryContext::current();
        $phoneRule = ['nullable', 'string', 'max:32'];
        if ($country?->phone_regex) {
            $phoneRule[] = 'regex:/'.$country->phone_regex.'/';
        } else {
            $phoneRule[] = 'regex:/^[0-9+\-\s()]+$/';
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => $phoneRule,
            'password' => ['required', 'min:8', 'confirmed'],
        ];

        if (\App\Models\Page::resolveLegal('terms') || \App\Models\Page::resolveLegal('privacy')) {
            // Acceptance required only when published legal pages exist.
            $terms = \App\Models\Page::resolveLegal('terms');
            $privacy = \App\Models\Page::resolveLegal('privacy');
            if ($terms?->status === 'published' || $terms?->is_published) {
                $rules['accept_terms'] = ['accepted'];
            }
            if ($privacy?->status === 'published' || $privacy?->is_published) {
                $rules['accept_privacy'] = ['accepted'];
            }
        }

        return $rules;
    }
}
