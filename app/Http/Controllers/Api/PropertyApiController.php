<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Property::where('is_active', true)->with('images');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $properties = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => $properties->getCollection()->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'location' => $p->location,
                'type' => $p->type,
                'price' => (float) $p->price,
                'primary_image_url' => $p->primary_image_url,
                'bedrooms' => $p->bedrooms,
                'bathrooms' => $p->bathrooms,
            ]),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ],
        ]);
    }
}
