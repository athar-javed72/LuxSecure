<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $houses = Listing::query()
            ->published()
            ->with(['images', 'listingType'])
            ->latest()
            ->take(12)
            ->get();

        return view('home', compact('houses'));
    }
}
