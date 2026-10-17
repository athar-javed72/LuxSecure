<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Favorites
 *
 * @authenticated
 */
class FavoriteController extends Controller
{
    /**
     * List favorited listings.
     */
    public function index(Request $request): JsonResponse
    {
        $listings = $request->user()
            ->favorites()
            ->with(['images', 'listingType'])
            ->latest('favorites.created_at')
            ->paginate(min((int) $request->get('per_page', 15), 50));

        $listings->getCollection()->transform(function (Listing $listing) {
            $listing->is_favorited = true;

            return $listing;
        });

        return response()->json([
            'success' => true,
            'data' => ListingResource::collection($listings->getCollection()),
            'meta' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ],
        ]);
    }

    /**
     * Toggle favorite for a listing.
     */
    public function toggle(Request $request, Listing $listing): JsonResponse
    {
        abort_unless($listing->status === 'published', 404);

        $attached = $request->user()->favorites()->toggle($listing->id);
        $isFavorited = ! empty($attached['attached']);

        return ApiResponse::success([
            'listing_id' => $listing->id,
            'favorited' => $isFavorited,
        ], $isFavorited ? 'Added to favorites' : 'Removed from favorites');
    }
}
