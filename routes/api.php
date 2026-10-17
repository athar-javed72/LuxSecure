<?php

use App\Http\Controllers\Api\ListingApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/properties', [ListingApiController::class, 'index']);

Route::middleware('auth:sanctum')->get('/properties/favorites', function (Request $request) {
    return $request->user()->favorites()->with('images')->get();
});
