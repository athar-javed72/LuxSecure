<?php

namespace App\Http\Requests\Admin;

use App\Models\RoomType;
use Illuminate\Foundation\Http\FormRequest;

class StoreSeasonalPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', RoomType::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'room_type_id' => ['required', 'exists:room_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
        ];
    }
}
