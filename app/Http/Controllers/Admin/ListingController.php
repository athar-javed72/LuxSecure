<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Models\City;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\ListingType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Listing::class);

        $query = Listing::with(['images', 'listingType', 'city'])->latest();

        $user = $request->user();
        if (! $user->can('listings.view.any')) {
            $query->where(function ($q) use ($user) {
                $q->where('agent_id', $user->id)->orWhere('owner_id', $user->id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('listing_type_id')) {
            $query->where('listing_type_id', $request->integer('listing_type_id'));
        }

        if ($request->filled('status') && in_array($request->status, Listing::STATUSES, true)) {
            $query->where('status', $request->status);
        }

        $listings = $query->paginate(15)->withQueryString();

        return view('admin.listings.index', [
            'listings' => $listings,
            'listingTypes' => ListingType::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Listing::class);

        return view('admin.listings.create', $this->formData());
    }

    public function store(StoreListingRequest $request): RedirectResponse
    {
        $this->authorize('create', Listing::class);

        $data = $request->safe()->except(['featured_image', 'images']);
        $data['attributes'] = $request->input('attributes', []);
        $data['slug'] = filled($data['slug'] ?? null)
            ? $data['slug']
            : Listing::uniqueSlug($data['title']);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('listings', 'public');
        }

        if (blank($data['agent_id'] ?? null) && $request->user()->hasRole('agent')) {
            $data['agent_id'] = $request->user()->id;
        }

        $listing = Listing::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('listings', 'public');
                ListingImage::create(['listing_id' => $listing->id, 'path' => $path, 'sort_order' => $i]);
            }
        }

        return redirect()->route('admin.listings.index')->with('success', 'Listing created.');
    }

    public function show(Listing $listing): View
    {
        $this->authorize('view', $listing);
        $listing->load(['images', 'listingType', 'city', 'area', 'owner', 'agent']);

        return view('admin.listings.show', compact('listing'));
    }

    public function edit(Listing $listing): View
    {
        $this->authorize('update', $listing);
        $listing->load('images');

        return view('admin.listings.edit', array_merge($this->formData(), compact('listing')));
    }

    public function update(UpdateListingRequest $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $data = $request->safe()->except(['featured_image', 'images']);
        $data['attributes'] = $request->input('attributes', []);
        $data['slug'] = filled($data['slug'] ?? null)
            ? $data['slug']
            : Listing::uniqueSlug($data['title'], $listing->id);

        if ($request->hasFile('featured_image')) {
            if ($listing->featured_image) {
                Storage::disk('public')->delete($listing->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('listings', 'public');
        }

        $listing->update($data);

        if ($request->hasFile('images')) {
            $sortStart = (int) $listing->images()->max('sort_order') + 1;
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('listings', 'public');
                ListingImage::create(['listing_id' => $listing->id, 'path' => $path, 'sort_order' => $sortStart + $i]);
            }
        }

        return redirect()->route('admin.listings.index')->with('success', 'Listing updated.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $this->authorize('delete', $listing);

        foreach ($listing->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        if ($listing->featured_image && ! str_starts_with($listing->featured_image, 'http')) {
            Storage::disk('public')->delete($listing->featured_image);
        }
        $listing->delete();

        return redirect()->route('admin.listings.index')->with('success', 'Listing deleted.');
    }

    /** @return array<string, mixed> */
    private function formData(): array
    {
        $types = ListingType::with('attributes')->where('is_active', true)->orderBy('name')->get();

        return [
            'listingTypes' => $types,
            'typesPayload' => $types->mapWithKeys(fn (ListingType $type) => [
                (string) $type->id => [
                    'id' => $type->id,
                    'name' => $type->name,
                    'attributes' => $type->attributes->map(fn ($a) => [
                        'key' => $a->key,
                        'label' => $a->label,
                        'input_type' => $a->input_type,
                        'options' => $a->options ?? [],
                        'is_required' => $a->is_required,
                    ])->values(),
                ],
            ]),
            'cities' => City::query()->where('is_active', true)->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(['id', 'name', 'email']),
            'purposes' => Listing::PURPOSES,
            'priceUnits' => Listing::PRICE_UNITS,
            'statuses' => Listing::STATUSES,
            'areaUnits' => Listing::AREA_UNITS,
        ];
    }
}
