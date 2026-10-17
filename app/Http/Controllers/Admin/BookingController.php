<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingStatusRequest;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Booking::class);

        $query = Booking::query()->with(['user', 'listing', 'roomType', 'room']);

        if (! $request->user()->can('bookings.view.any')) {
            $query->whereHas('listing', function ($q) use ($request) {
                $q->where('owner_id', $request->user()->id)
                    ->orWhere('agent_id', $request->user()->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);
        $booking->load(['user', 'listing', 'roomType', 'room', 'payments']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(UpdateBookingStatusRequest $request, Booking $booking): RedirectResponse
    {
        $booking->update(['status' => $request->validated('status')]);
        $booking->load(['user', 'listing', 'roomType']);

        Mail::to($booking->user->email)->queue(new BookingConfirmationMail($booking));

        return back()->with('success', 'Booking status updated.');
    }
}
