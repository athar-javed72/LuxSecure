<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\StoreRoomTypeRequest;
use App\Http\Requests\Admin\StoreSeasonalPriceRequest;
use App\Models\Listing;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SeasonalPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', RoomType::class);

        $query = RoomType::query()->with(['listing', 'rooms'])->withCount('rooms');

        if (! $request->user()->can('listings.view.any')) {
            $query->whereHas('listing', function ($q) use ($request) {
                $q->where('owner_id', $request->user()->id)
                    ->orWhere('agent_id', $request->user()->id);
            });
        }

        $roomTypes = $query->latest()->paginate(15);

        return view('admin.room-types.index', compact('roomTypes'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', RoomType::class);
        $listings = $this->scopedHotelListings($request->user());

        return view('admin.room-types.create', compact('listings'));
    }

    public function store(StoreRoomTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $listing = Listing::findOrFail($data['listing_id']);

        if (! $request->user()->can('listings.update.any') && ! $request->user()->ownsListing($listing)) {
            abort(403);
        }

        $roomType = RoomType::create([
            'listing_id' => $listing->id,
            'name' => $data['name'],
            'capacity' => $data['capacity'],
            'base_price' => $data['base_price'],
            'amenities' => $data['amenities'] ?? [],
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()
            ->route('admin.room-types.show', $roomType)
            ->with('success', 'Room type created.');
    }

    public function show(RoomType $room_type): View
    {
        $this->authorize('view', $room_type);
        $room_type->load(['listing', 'rooms', 'seasonalPrices']);

        return view('admin.room-types.show', ['roomType' => $room_type]);
    }

    public function storeRoom(StoreRoomRequest $request): RedirectResponse
    {
        $roomType = RoomType::with('listing')->findOrFail($request->validated('room_type_id'));
        $this->authorize('update', $roomType);

        Room::create([
            'room_type_id' => $roomType->id,
            'listing_id' => $roomType->listing_id,
            'number' => $request->validated('number'),
            'status' => $request->validated('status') ?? 'available',
        ]);

        return back()->with('success', 'Room added.');
    }

    public function storeSeasonalPrice(StoreSeasonalPriceRequest $request): RedirectResponse
    {
        $roomType = RoomType::with('listing')->findOrFail($request->validated('room_type_id'));
        $this->authorize('update', $roomType);

        SeasonalPrice::create($request->validated());

        return back()->with('success', 'Seasonal price added.');
    }

    public function destroy(RoomType $room_type): RedirectResponse
    {
        $this->authorize('delete', $room_type);
        $room_type->delete();

        return redirect()
            ->route('admin.room-types.index')
            ->with('success', 'Room type deleted.');
    }

    private function scopedHotelListings($user)
    {
        $query = Listing::query()
            ->whereIn('purpose', ['short_stay', 'rent'])
            ->orderBy('title');

        if (! $user->can('listings.view.any')) {
            $query->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)->orWhere('agent_id', $user->id);
            });
        }

        return $query->get(['id', 'title']);
    }
}
