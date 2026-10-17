<?php

namespace App\Http\Requests\Admin;

use App\Models\Tenancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tenancies.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'listing_id' => ['required', 'exists:listings,id'],
            'tenant_id' => ['required', 'exists:users,id'],
            'landlord_id' => ['nullable', 'exists:users,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'monthly_rent' => ['required', 'numeric', 'min:0'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(Tenancy::STATUSES)],
        ];
    }
}
