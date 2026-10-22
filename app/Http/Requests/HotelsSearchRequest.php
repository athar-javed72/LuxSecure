<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class HotelsSearchRequest extends FormRequest
{
    public const MAX_GUESTS = 4;

    /** Inclusive night count: check_in → check_out exclusive of checkout day. */
    public const MAX_NIGHTS = 30;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('destination') && is_string($this->input('destination'))) {
            $this->merge([
                'destination' => trim($this->input('destination')),
            ]);
        }

        if ($this->filled('guests')) {
            $this->merge([
                'guests' => (int) $this->input('guests'),
            ]);
        }
    }

    public function rules(): array
    {
        if (! $this->isSearchAttempt()) {
            return [];
        }

        return [
            'destination' => ['nullable', 'string', 'max:120'],
            'check_in' => ['nullable', 'required_with:check_out', 'date', 'after_or_equal:today'],
            'check_out' => ['nullable', 'required_with:check_in', 'date', 'after:check_in'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:'.self::MAX_GUESTS],
            'sort' => ['nullable', 'in:price_asc'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('check_in') || ! $this->filled('check_out')) {
                return;
            }

            if ($validator->errors()->has('check_in') || $validator->errors()->has('check_out')) {
                return;
            }

            try {
                $tz = config('app.timezone');
                $in = Carbon::parse($this->input('check_in'), $tz)->startOfDay();
                $out = Carbon::parse($this->input('check_out'), $tz)->startOfDay();
            } catch (\Throwable) {
                return;
            }

            if ($out->lte($in)) {
                $validator->errors()->add('check_out', 'Check-out must be at least one night after check-in.');

                return;
            }

            $nights = $in->diffInDays($out);
            if ($nights > self::MAX_NIGHTS) {
                $validator->errors()->add(
                    'check_out',
                    'Stays cannot exceed '.self::MAX_NIGHTS.' nights. Please shorten your dates.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'check_in.after_or_equal' => 'Check-in must be today or a future date.',
            'check_out.after' => 'Check-out must be at least one night after check-in.',
            'check_in.required_with' => 'Please choose a check-in date.',
            'check_out.required_with' => 'Please choose a check-out date.',
            'guests.min' => 'At least 1 guest is required.',
            'guests.max' => 'Guest count cannot exceed '.self::MAX_GUESTS.'.',
        ];
    }

    public function isSearchAttempt(): bool
    {
        return $this->hasAny(['destination', 'check_in', 'check_out', 'guests']);
    }

    /**
     * Stay on Hotels. Flash old input (no invalid query echo — avoids a validate loop).
     */
    protected function failedValidation(Validator $validator): void
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo(route('divisions.hospitality'));
    }
}
