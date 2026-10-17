<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListingApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Listing::query()->published()->with(['images', 'listingType']);

        if ($request->filled('search')) {
            $q = $request->search;
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
        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $listings = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => $listings->getCollection()->map(fn (Listing $p) => [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'location' => $p->location,
                'type' => $p->type_name,
                'purpose' => $p->purpose,
                'price' => (float) $p->price,
                'price_unit' => $p->price_unit,
                'currency' => $p->currency,
                'primary_image_url' => $p->primary_image_url,
                'bedrooms' => $p->bedrooms,
                'bathrooms' => $p->bathrooms,
                'attributes' => $p->attributeValues(),
            ]),
            'meta' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ],
        ]);
    }
}
