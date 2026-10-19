<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function hospitality(): View
    {
        $name = Setting::getValue('site.division.hotels', 'Hotels');

        return view('divisions.show', [
            'title' => $name,
            'division' => 'hotels',
            'intro' => 'Hospitality designed for considered stays — boutique hotels and short-stay residences under the '.$name.' division.',
            'sectionTitle' => 'Hotels',
            'sectionSubtitle' => 'Short stays with the same care as a private residence.',
            'body' => 'Explore curated hotel and short-stay listings. Bookings, room types, and cancellation policies follow country-aware commerce settings.',
            'ctaHref' => '/properties?purpose=short_stay',
            'ctaLabel' => 'Browse stays',
        ]);
    }

    public function living(): View
    {
        $name = Setting::getValue('site.division.living', 'Living');

        return view('divisions.show', [
            'title' => $name,
            'division' => 'living',
            'intro' => 'Longer-term homes and rental living managed with clarity — tenancies, schedules, and trusted local support.',
            'sectionTitle' => 'Living',
            'sectionSubtitle' => 'Rentals and residences for everyday life.',
            'body' => 'Discover rent listings and tenancy-ready homes. '.$name.' focuses on transparent pricing and dependable landlord–tenant workflows.',
            'ctaHref' => '/properties?purpose=rent',
            'ctaLabel' => 'Browse rentals',
        ]);
    }
}
