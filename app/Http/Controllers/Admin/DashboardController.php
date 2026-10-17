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
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalProperties' => Listing::count(),
            'totalInquiries' => ContactInquiry::count(),
            'unreadInquiries' => ContactInquiry::where('is_read', false)->count(),
            'recentProperties' => Listing::with('listingType')->latest()->take(5)->get(),
            'recentInquiries' => ContactInquiry::latest()->take(5)->get(),
        ]);
    }
}
