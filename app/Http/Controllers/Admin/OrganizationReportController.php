<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\Organization;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrganizationReportController extends Controller
{
    public function show(Request $request, Organization $organization): View
    {
        Gate::authorize('organizations.view');

        $user = $request->user();
        if ($user->isCountryScoped() && $organization->country_id && (int) $organization->country_id !== (int) $user->country_id) {
            abort(403);
        }

        if ($user->hasRole('organization_admin') && (int) $user->organization_id !== (int) $organization->id) {
            abort(403);
        }

        $branchIds = Listing::query()
            ->where('organization_id', $organization->id)
            ->when($user->isCountryScoped(), fn ($q) => $q->where('country_id', $user->country_id))
            ->pluck('id');

        $bookingIds = Booking::query()->whereIn('listing_id', $branchIds)->pluck('id');

        $stats = [
            'branches' => $branchIds->count(),
            'bookings_total' => $bookingIds->count(),
            'bookings_pending' => Booking::query()->whereIn('id', $bookingIds)->where('status', 'pending')->count(),
            'bookings_confirmed' => Booking::query()->whereIn('id', $bookingIds)->where('status', 'confirmed')->count(),
            'bookings_cancelled' => Booking::query()->whereIn('id', $bookingIds)->where('status', 'cancelled')->count(),
            'payments_paid' => Payment::query()->where('payable_type', Booking::class)->whereIn('payable_id', $bookingIds)->where('status', 'paid')->count(),
            'revenue_paid' => (float) Payment::query()->where('payable_type', Booking::class)->whereIn('payable_id', $bookingIds)->where('status', 'paid')->sum('amount'),
            'revenue_pending' => (float) Payment::query()->where('payable_type', Booking::class)->whereIn('payable_id', $bookingIds)->whereIn('status', ['pending', 'awaiting_approval'])->sum('amount'),
        ];

        $byBranch = Listing::query()
            ->whereIn('id', $branchIds)
            ->withCount([
                'bookings',
                'bookings as confirmed_bookings_count' => fn ($q) => $q->where('status', 'confirmed'),
            ])
            ->orderBy('branch_name')
            ->orderBy('title')
            ->get();

        $organization->loadMissing('country');

        return view('admin.organizations.report', compact('organization', 'stats', 'byBranch'));
    }
}
