<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateListingTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('listing_types.update') ?? false;
    }

    public function rules(): array
    {
        $type = $this->route('listing_type');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('listing_types', 'slug')->ignore($type?->id)],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'attributes' => ['nullable', 'array'],
            'attributes.*.id' => ['nullable', 'integer', 'exists:type_attributes,id'],
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
