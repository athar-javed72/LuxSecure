<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\RoomType;
use App\Services\Hotel\BookingAvailabilityService;
use App\Services\Hotel\BookingPricingService;
use App\Services\Payments\PaymentLedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingAvailabilityService $availability,
        private readonly BookingPricingService $pricing,
        private readonly PaymentLedgerService $ledger,
    ) {}

    public function create(Listing $listing, Request $request): View
    {
        abort_unless($listing->status === 'published', 404);

        $roomTypes = $listing->roomTypes()->where('is_active', true)->with('seasonalPrices')->get();
        abort_if($roomTypes->isEmpty(), 404);

        return view('bookings.create', [
            'listing' => $listing,
            'roomTypes' => $roomTypes,
            'selectedRoomTypeId' => $request->integer('room_type_id') ?: $roomTypes->first()->id,
        ]);
    }

    public function quote(Request $request): JsonResponse
    {
        $data = $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        $roomType = RoomType::with('seasonalPrices')->findOrFail($data['room_type_id']);
        $quote = $this->pricing->calculate($roomType, $data['check_in'], $data['check_out']);
        $available = $this->availability->isAvailable($roomType, $data['check_in'], $data['check_out']);

        return response()->json([
            'available' => $available,
            'available_rooms' => $this->availability->availableRoomCount($roomType, $data['check_in'], $data['check_out']),
            ...$quote,
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $roomType = RoomType::with(['seasonalPrices', 'listing'])->findOrFail($data['room_type_id']);

        abort_unless($roomType->is_active && $roomType->listing?->status === 'published', 404);

        $booking = $this->availability->createBooking($roomType, $data['check_in'], $data['check_out'], [
            'user_id' => $request->user()->id,
            'guests' => $data['guests'],
            'cancellation_policy' => $data['cancellation_policy'] ?? 'flexible',
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        $this->ledger->createPendingForBooking($booking);

        $booking->load(['user', 'listing', 'roomType']);
        Mail::to($booking->user->email)->queue(new BookingConfirmationMail($booking));

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Booking submitted. Complete payment before the hold expires. Confirmation email queued.');
    }

    public function mine(Request $request): View
    {
        $this->authorize('viewAny', Booking::class);

        $bookings = Booking::query()
            ->with(['listing', 'roomType', 'room', 'payments'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return view('bookings.mine', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);
        $booking->load(['listing', 'roomType', 'room', 'payments']);

        return view('bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $this->authorize('cancelOwn', $booking);
        $this->ledger->cancelBooking($booking, request()->user());
        $booking->load(['user', 'listing', 'roomType']);
        Mail::to($booking->user->email)->queue(new BookingConfirmationMail($booking));

        return back()->with('success', 'Booking cancelled.');
    }
}
