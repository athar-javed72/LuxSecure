<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use App\Models\Property;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalProperties' => Property::count(),
            'totalInquiries' => ContactInquiry::count(),
            'unreadInquiries' => ContactInquiry::where('is_read', false)->count(),
            'recentProperties' => Property::latest()->take(5)->get(),
            'recentInquiries' => ContactInquiry::latest()->take(5)->get(),
        ]);
    }
}
