<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use App\Models\Listing;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $listings = Listing::query()->forUserCountry($user);
        if (! $user->can('listings.view.any')) {
            $listings->where(function ($q) use ($user) {
                $q->where('agent_id', $user->id)
                    ->orWhere('owner_id', $user->id)
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

        $users = User::query()->forUserCountry($user);
        $inquiries = ContactInquiry::query()
            ->when($user->isCountryScoped(), function ($q) use ($user) {
                $q->whereHas('listing', fn ($l) => $l->where('country_id', $user->country_id));
            });

        return view('admin.dashboard', [
            'totalUsers' => (clone $users)->count(),
            'totalProperties' => (clone $listings)->count(),
            'totalInquiries' => (clone $inquiries)->count(),
            'unreadInquiries' => (clone $inquiries)->where('is_read', false)->count(),
            'recentProperties' => (clone $listings)->with('listingType')->latest()->take(5)->get(),
            'recentInquiries' => (clone $inquiries)->latest()->take(5)->get(),
        ]);
    }
}
