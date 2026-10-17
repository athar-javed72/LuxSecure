<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Booking */
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'listing_id' => $this->listing_id,
            'room_type_id' => $this->room_type_id,
            'room_id' => $this->room_id,
            'check_in' => optional($this->check_in)?->toDateString(),
            'check_out' => optional($this->check_out)?->toDateString(),
            'guests' => $this->guests,
            'total' => (float) $this->total,
            'status' => $this->status,
            'cancellation_policy' => $this->cancellation_policy,
            'listing' => new ListingResource($this->whenLoaded('listing')),
            'room_type' => $this->whenLoaded('roomType', fn () => [
                'id' => $this->roomType->id,
                'name' => $this->roomType->name,
                'capacity' => $this->roomType->capacity,
            ]),
            'created_at' => optional($this->created_at)?->toIso8601String(),
        ];
    }
}
