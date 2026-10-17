<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\V1\ListingController as V1ListingController;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Legacy /api/properties endpoint — delegates to v1 listings index.
 */
class ListingApiController extends Controller
{
    public function index(Request $request, V1ListingController $listings): JsonResponse
    {
        $response = $listings->index($request);
        $payload = $response->getData(true);

        // Preserve historical shape for older clients.
        return response()->json([
            'data' => $payload['data'] ?? [],
            'meta' => $payload['meta'] ?? [],
        ], $response->getStatusCode());
    }
}
