<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\Review;
use App\Models\Tenancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Listing $listing): RedirectResponse
    {
        $user = $request->user();
        $this->assertEligible($user->id, $listing->id);

        $data = $request->validated();

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
