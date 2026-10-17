<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Listing $listing): RedirectResponse
    {
        $user = $request->user();
        if ($user->favorites()->where('listing_id', $listing->id)->exists()) {
            $user->favorites()->detach($listing->id);
            $message = 'Removed from favorites.';
        } else {
            $user->favorites()->attach($listing->id);
            $message = 'Added to favorites.';
        }

        return back()->with('success', $message);
    }
}
