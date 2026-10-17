<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $houses = Property::where('is_active', true)
            ->with('images')
            ->latest()
            ->take(12)
            ->get();

        return view('home', compact('houses'));
    }
}
