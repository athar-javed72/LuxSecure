<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Listing;
use App\Models\Review;
use App\Models\Tenancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('create', Review::class);

        $user = $request->user();
        $this->assertEligible($user->id, $listing->id);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['nullable', 'string', 'max:2000'],
        ]);

        Review::query()->updateOrCreate(
            ['listing_id' => $listing->id, 'user_id' => $user->id],
            [
                'agent_id' => $listing->agent_id,
                'rating' => $data['rating'],
                'body' => $data['body'] ?? null,
                'status' => 'pending',
            ]
        );

        return back()->with('success', 'Review submitted for moderation.');
    }

    private function assertEligible(int $userId, int $listingId): void
    {
        $completedBooking = Booking::query()
            ->where('user_id', $userId)
            ->where('listing_id', $listingId)
            ->where('status', 'completed')
            ->exists();

        $tenancy = Tenancy::query()
            ->where('tenant_id', $userId)
            ->where('listing_id', $listingId)
            ->whereIn('status', ['active', 'ended'])
            ->exists();

        if (! $completedBooking && ! $tenancy) {
            throw ValidationException::withMessages([
                'rating' => 'Only guests with a completed booking or tenancy can review this listing.',
            ]);
        }
    }
}
