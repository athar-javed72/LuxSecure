<?php

namespace App\Http\Requests\Admin;

use App\Models\Tenancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Tenancy $tenancy */
        $tenancy = $this->route('tenancy');

        return $this->user()?->can('update', $tenancy) ?? false;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'monthly_rent' => ['sometimes', 'numeric', 'min:0'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::in(Tenancy::STATUSES)],
        ];
    }
}
