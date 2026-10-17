<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MarkRentPaidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('rent_payment')) ?? false;
    }

    public function rules(): array
    {
        return [
            'method' => ['nullable', 'string', 'max:64'],
        ];
    }
}
