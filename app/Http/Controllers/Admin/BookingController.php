<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingStatusRequest;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Country;
use App\Notifications\BookingStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Booking::class);

        $user = $request->user();
        $query = Booking::query()->with(['user', 'listing', 'roomType', 'room'])->forUserCountry($user);

        if (! $user->can('bookings.view.any')) {
            $query->whereHas('listing', function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhere('agent_id', $user->id)
                    ->orWhere('manager_id', $user->id)
                    ->orWhere(function ($org) use ($user) {
                        if ($user->hasRole('organization_admin') && $user->organization_id) {
                            $org->where('organization_id', $user->organization_id);
                        } else {
                            $org->whereRaw('1 = 0');
                        }
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('country_id')) {
            $query->whereHas('listing', fn ($q) => $q->where('country_id', $request->integer('country_id')));
        }

        if ($request->filled('city_id')) {
            $query->whereHas('listing', fn ($q) => $q->where('city_id', $request->integer('city_id')));
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'filterCountries' => Country::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
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
        $booking->user->notify(new BookingStatusNotification($booking));

        return back()->with('success', 'Booking status updated.');
    }
}
