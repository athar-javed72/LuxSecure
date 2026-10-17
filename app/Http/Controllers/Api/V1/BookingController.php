<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\RoomType;
use App\Services\Hotel\BookingAvailabilityService;
use App\Services\Payments\PaymentRefundService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

/**
 * @group Bookings
 *
 * @authenticated
 */
class BookingController extends Controller
{
    public function __construct(
        private readonly BookingAvailabilityService $availability,
        private readonly PaymentRefundService $refunds,
    ) {}

    /**
     * List the authenticated user's bookings.
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::query()
            ->with(['listing.images', 'listing.listingType', 'roomType'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(min((int) $request->get('per_page', 15), 50));

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings->getCollection()),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    /**
     * Create a booking (availability checked in a transaction).
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('bookings.create'), 403);

        $data = $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:50'],
            'cancellation_policy' => ['nullable', Rule::in(Booking::CANCELLATION_POLICIES)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $roomType = RoomType::with(['seasonalPrices', 'listing'])->findOrFail($data['room_type_id']);
        abort_unless($roomType->is_active && $roomType->listing?->status === 'published', 404);

        $booking = $this->availability->createBooking($roomType, $data['check_in'], $data['check_out'], [
            'user_id' => $request->user()->id,
            'guests' => $data['guests'],
            'cancellation_policy' => $data['cancellation_policy'] ?? 'flexible',
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        BookingPayment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total,
            'status' => 'pending',
            'method' => 'pending',
        ]);

        $booking->load(['user', 'listing', 'roomType']);
        Mail::to($booking->user->email)->queue(new BookingConfirmationMail($booking));

        return ApiResponse::success(new BookingResource($booking->load(['listing.images', 'listing.listingType', 'roomType'])), 'Booking created', 201);
    }

    /**
     * Show one of the user's bookings.
     */
    public function show(Request $request, Booking $booking): JsonResponse
    {
        abort_unless((int) $booking->user_id === (int) $request->user()->id, 403);
        $booking->load(['listing.images', 'listing.listingType', 'roomType', 'room']);

        return ApiResponse::success(new BookingResource($booking));
    }

    /**
     * Cancel an own pending/confirmed booking.
     */
    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        abort_unless((int) $booking->user_id === (int) $request->user()->id, 403);
        abort_unless(in_array($booking->status, ['pending', 'confirmed'], true), 422, 'Booking cannot be cancelled.');

        $booking->update(['status' => 'cancelled']);
        $this->refunds->voidOrMarkRefundPending($booking);
        $booking->load(['user', 'listing', 'roomType']);
        Mail::to($booking->user->email)->queue(new BookingConfirmationMail($booking));

        return ApiResponse::success(new BookingResource($booking), 'Booking cancelled');
    }
}
