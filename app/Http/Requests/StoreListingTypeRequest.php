<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreListingTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:listing_types,slug'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'attributes' => ['nullable', 'array'],
            'attributes.*.key' => ['required_with:attributes', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/'],
            'attributes.*.label' => ['required_with:attributes', 'string', 'max:255'],
            'attributes.*.input_type' => ['required_with:attributes', Rule::in(['text', 'number', 'select', 'boolean'])],
            'attributes.*.options' => ['nullable', 'string'],
            'attributes.*.is_required' => ['nullable', 'boolean'],
            'attributes.*.is_filterable' => ['nullable', 'boolean'],
            'attributes.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('slug') && $this->filled('name')) {
            $this->merge(['slug' => Str::slug($this->input('name'))]);
        }
    }
}
