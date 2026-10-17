<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Property $property): RedirectResponse
    {
        $user = $request->user();
        if ($user->favorites()->where('property_id', $property->id)->exists()) {
            $user->favorites()->detach($property->id);
            $message = 'Removed from favorites.';
        } else {
            $user->favorites()->attach($property->id);
            $message = 'Added to favorites.';
        }

        return back()->with('success', $message);
    }
}
