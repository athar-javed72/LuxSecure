<?php

namespace App\Http\Requests;

use App\Models\Listing;
use App\Models\ListingType;
use App\Support\CountryContext;
use App\Support\ListingAttributeRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('listings.create') ?? false;
    }

    public function rules(): array
    {
        $type = ListingType::with('attributes')->find($this->input('listing_type_id'));

        return array_merge([
            'listing_type_id' => ['required', 'exists:listing_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:listings,slug'],
            'location' => ['required', 'string', 'max:255'],
            'purpose' => ['required', Rule::in(Listing::PURPOSES)],
            'price' => ['required', 'numeric', 'min:0'],
            'price_unit' => ['required', Rule::in(Listing::PRICE_UNITS)],
            'currency' => ['nullable', 'string', 'max:10'],
            'status' => ['required', Rule::in(Listing::STATUSES)],
            'city_id' => ['nullable', 'exists:cities,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'area_size' => ['nullable', 'numeric', 'min:0'],
            'area_unit' => ['nullable', Rule::in(Listing::AREA_UNITS)],
            'owner_id' => ['nullable', 'exists:users,id'],
            'agent_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'floor_plan' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'private_document' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ], ListingAttributeRules::forType($type));
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('attributes') && is_array($this->input('attributes'))) {
            $attrs = $this->input('attributes');
            foreach ($attrs as $key => $value) {
                if ($value === '1' || $value === '0' || $value === 1 || $value === 0) {
                    // leave as-is; boolean validation accepts 1/0
                }
                if ($value === '') {
                    $attrs[$key] = null;
                }
            }
            $this->merge(['attributes' => $attrs]);
        }

        if (! $this->filled('price_unit') && $this->filled('purpose')) {
            $this->merge([
                'price_unit' => match ($this->input('purpose')) {
                    'rent' => 'per_month',
                    'short_stay' => 'per_night',
                    default => 'total',
                },
            ]);
        }

        if (! $this->filled('currency')) {
            $this->merge([
                'currency' => CountryContext::current()?->currency_code ?: 'PKR',
            ]);
        }
        if (! $this->filled('country_id')) {
            $this->merge([
                'country_id' => CountryContext::current()?->id,
            ]);
        }
    }
}
