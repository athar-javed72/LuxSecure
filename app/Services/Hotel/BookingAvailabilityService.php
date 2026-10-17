<?php

namespace App\Services\Hotel;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingAvailabilityService
{
    public function __construct(
        private readonly BookingPricingService $pricing
    ) {}

    public function overlappingBookingsQuery(int $roomTypeId, Carbon|string $checkIn, Carbon|string $checkOut, ?int $ignoreBookingId = null)
    {
        $checkIn = Carbon::parse($checkIn)->toDateString();
        $checkOut = Carbon::parse($checkOut)->toDateString();

        return Booking::query()
            ->where('room_type_id', $roomTypeId)
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId));
    }

    public function availableRoomCount(RoomType $roomType, Carbon|string $checkIn, Carbon|string $checkOut, ?int $ignoreBookingId = null): int
    {
        $totalRooms = $roomType->rooms()->where('status', 'available')->count();
        $booked = $this->overlappingBookingsQuery($roomType->id, $checkIn, $checkOut, $ignoreBookingId)->count();

        return max(0, $totalRooms - $booked);
    }

    public function isAvailable(RoomType $roomType, Carbon|string $checkIn, Carbon|string $checkOut, ?int $ignoreBookingId = null): bool
    {
        return $this->availableRoomCount($roomType, $checkIn, $checkOut, $ignoreBookingId) > 0;
    }

    /**
     * Create a booking inside a DB transaction with row locks to prevent double booking.
     *
     * @param  array{user_id: int, guests: int, cancellation_policy?: string, notes?: string|null, status?: string}  $data
     */
    public function createBooking(RoomType $roomType, string $checkIn, string $checkOut, array $data): Booking
    {
        return DB::transaction(function () use ($roomType, $checkIn, $checkOut, $data) {
            $checkInDate = Carbon::parse($checkIn)->startOfDay();
            $checkOutDate = Carbon::parse($checkOut)->startOfDay();

            if ($checkOutDate->lte($checkInDate)) {
                throw ValidationException::withMessages([
                    'check_out' => 'Check-out must be after check-in.',
                ]);
            }

            if ((int) $data['guests'] > (int) $roomType->capacity) {
                throw ValidationException::withMessages([
                    'guests' => 'Guest count exceeds room capacity.',
                ]);
            }

            // Lock inventory rows for this room type.
            $rooms = Room::query()
                ->where('room_type_id', $roomType->id)
                ->where('status', 'available')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($rooms->isEmpty()) {
                throw ValidationException::withMessages([
                    'room_type_id' => 'No rooms available for this type.',
                ]);
            }

            $overlapping = $this->overlappingBookingsQuery($roomType->id, $checkInDate, $checkOutDate)
                ->lockForUpdate()
                ->get();

            $occupiedRoomIds = $overlapping->pluck('room_id')->filter()->all();
            $freeRoom = $rooms->first(fn (Room $room) => ! in_array($room->id, $occupiedRoomIds, true));

            // If older bookings have no room assigned, fall back to count check.
            if (! $freeRoom || $overlapping->count() >= $rooms->count()) {
                throw ValidationException::withMessages([
                    'check_in' => 'Selected dates are not available for this room type.',
                ]);
            }

            $quote = $this->pricing->calculate($roomType, $checkInDate, $checkOutDate);

            return Booking::create([
                'user_id' => $data['user_id'],
                'listing_id' => $roomType->listing_id,
                'room_type_id' => $roomType->id,
                'room_id' => $freeRoom->id,
                'check_in' => $checkInDate->toDateString(),
                'check_out' => $checkOutDate->toDateString(),
                'guests' => $data['guests'],
                'total' => $quote['total'],
                'status' => $data['status'] ?? 'pending',
                'cancellation_policy' => $data['cancellation_policy'] ?? 'flexible',
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
