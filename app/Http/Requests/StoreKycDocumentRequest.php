<?php

namespace App\Http\Requests;

use App\Models\KycDocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKycDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->can('kyc.submit') || $user->can('kyc.create'));
    }

    public function rules(): array
    {
        $user = $this->user();
        $countryId = $user?->country_id;

        $allowed = KycDocumentType::query()
            ->where('is_active', true)
            ->when($countryId, fn ($q) => $q->where('country_id', $countryId))
            ->pluck('code')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // Legacy PK aliases if types not yet seeded for the user country.
        if ($allowed === []) {
            $allowed = ['cnic', 'license', 'registry'];
        }

        return [
            'type' => ['required', 'string', Rule::in($allowed)],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
        ];
    }
}
