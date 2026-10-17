<?php

namespace App\Http\Requests;

use App\Models\KycDocument;
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
        return [
            'type' => ['required', Rule::in(KycDocument::TYPES)],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
        ];
    }
}
