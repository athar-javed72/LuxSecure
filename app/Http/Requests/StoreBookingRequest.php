<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\CancellationPolicy;
use App\Support\LegalAcceptanceService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('bookings.create') ?? false;
    }

    public function rules(): array
    {
        $policySlugs = CancellationPolicy::query()
            ->where('is_active', true)
            ->pluck('slug')
            ->merge(Booking::CANCELLATION_POLICIES)
            ->unique()
            ->values()
            ->all();

        return array_merge([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:50'],
            'cancellation_policy' => ['nullable', Rule::in($policySlugs)],
            'cancellation_policy_id' => ['nullable', 'exists:cancellation_policies,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], LegalAcceptanceService::validationRules());
    }
}
