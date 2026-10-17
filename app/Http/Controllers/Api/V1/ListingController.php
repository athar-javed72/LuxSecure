<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Services\ListingCacheService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Listings
 */
class ListingController extends Controller
{
    public function __construct(private readonly ListingCacheService $cache) {}

    /**
     * Paginated published listings.
     *
     * @unauthenticated
     */
    public function index(Request $request): JsonResponse
    {
        $paginator = $this->cache->rememberIndex($request, function () use ($request) {
            $query = Listing::query()->published()->with(['images', 'listingType', 'country', 'organization']);

            if ($request->filled('search')) {
                $q = $request->string('search')->toString();
                $query->where(function ($qry) use ($q) {
                    $qry->where('title', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%");
                });
            }
            if ($request->filled('type')) {
                $type = $request->string('type')->toString();
                if (ctype_digit($type)) {
                    $query->where('listing_type_id', (int) $type);
                } else {
                    $query->whereHas('listingType', fn ($t) => $t->where('slug', $type));
                }
            }
            if ($request->filled('purpose') && in_array($request->purpose, Listing::PURPOSES, true)) {
                $query->where('purpose', $request->purpose);
            }
            if ($request->filled('city_id')) {
                $query->where('city_id', $request->integer('city_id'));
            }
            if ($request->filled('country_id')) {
                $query->where('country_id', $request->integer('country_id'));
            } elseif ($request->filled('country')) {
                $iso = strtoupper($request->string('country')->toString());
                $query->whereHas('country', fn ($c) => $c->where('iso2', $iso));
            }
            if ($request->filled('price_min')) {
                $query->where('price', '>=', (float) $request->price_min);
            }
            if ($request->filled('price_max')) {
                $query->where('price', '<=', (float) $request->price_max);
            }

            $perPage = min(max((int) $request->get('per_page', 15), 1), 50);

            return $query->latest()->paginate($perPage);
        });

        if ($request->user()) {
            $favIds = $request->user()->favorites()->pluck('listings.id')->all();
            $paginator->getCollection()->transform(function (Listing $listing) use ($favIds) {
                $listing->is_favorited = in_array($listing->id, $favIds, true);

                return $listing;
            });
        }

        return response()->json([
            'success' => true,
            'data' => ListingResource::collection($paginator->getCollection()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Show a published listing.
     *
     * @unauthenticated
     */
    public function show(Request $request, string $listing): JsonResponse
    {
        $model = $this->cache->rememberShow($listing, function () use ($listing) {
            return Listing::query()
                ->published()
                ->with(['images', 'listingType', 'city', 'area'])
                ->where(function ($q) use ($listing) {
                    if (ctype_digit($listing)) {
                        $q->where('id', (int) $listing);
                    } else {
                        $q->where('slug', $listing);
                    }
                })
                ->firstOrFail();
        });

        if ($request->user()) {
            $model->is_favorited = $request->user()->favorites()->where('listing_id', $model->id)->exists();
        }

        return ApiResponse::success(new ListingResource($model));
    }
}
