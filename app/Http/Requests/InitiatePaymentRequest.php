<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('payments.create') ?? false;
    }

    public function rules(): array
    {
        $gateway = $this->input('gateway');

        return [
            'payable_type' => ['required', Rule::in(['booking', 'rent_payment'])],
            'payable_id' => ['required', 'integer'],
            'gateway' => ['required', Rule::in(Payment::enabledGateways())],
            'idempotency_key' => ['nullable', 'string', 'max:100'],
            'bank_reference' => ['nullable', 'string', 'max:120'],
            'proof' => [
                Rule::requiredIf($gateway === 'bank_transfer'),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ];
    }
}
