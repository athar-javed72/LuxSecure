<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\ListingType;
use App\Models\User;
use App\Services\ImageOptimizer;
use App\Services\ListingCacheService;
use App\Support\CsvExporter;
use App\Support\StorageDisk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListingController extends Controller
{
    public function __construct(
        private readonly ImageOptimizer $images,
        private readonly ListingCacheService $listingCache,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Listing::class);

        $query = Listing::with(['images', 'listingType', 'city'])->latest();

        $user = $request->user();
        $query->forUserCountry($user);
        if (! $user->can('listings.view.any')) {
            $query->where(function ($q) use ($user) {
                $q->where('agent_id', $user->id)
                    ->orWhere('owner_id', $user->id)
                    ->orWhere('manager_id', $user->id)
                    ->orWhere(function ($org) use ($user) {
                        if ($user->organization_id) {
                            $org->where('organization_id', $user->organization_id);
                        }
                    });
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

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->integer('country_id'));
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }

        $listings = $query->paginate(15)->withQueryString();

        return view('admin.listings.index', [
            'listings' => $listings,
            'listingTypes' => ListingType::query()->where('is_active', true)->orderBy('name')->get(),
            'filterCountries' => Country::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
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

        $data = $request->safe()->except(['featured_image', 'images', 'floor_plan', 'private_document']);
        $data['attributes'] = $request->input('attributes', []);
        $data['slug'] = filled($data['slug'] ?? null)
            ? $data['slug']
            : Listing::uniqueSlug($data['title']);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->images->store($request->file('featured_image'));
        }

        if ($request->hasFile('floor_plan')) {
            $data['floor_plan_path'] = $request->file('floor_plan')->store('listings/floor-plans', StorageDisk::uploads());
        }

        if ($request->hasFile('private_document')) {
            $data['private_document_path'] = $request->file('private_document')->store('listings/private', StorageDisk::private());
        }

        if (blank($data['agent_id'] ?? null) && $request->user()->hasRole('agent')) {
            $data['agent_id'] = $request->user()->id;
        }

        $data = $this->enforceKycPendingStatus($data, $request->user());

        $listing = Listing::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $this->images->store($file);
                ListingImage::create(['listing_id' => $listing->id, 'path' => $path, 'sort_order' => $i]);
            }
        }

        $this->listingCache->flush();

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

        $data = $request->safe()->except(['featured_image', 'images', 'floor_plan', 'private_document']);
        $data['attributes'] = $request->input('attributes', []);
        $data['slug'] = filled($data['slug'] ?? null)
            ? $data['slug']
            : Listing::uniqueSlug($data['title'], $listing->id);

        if ($request->hasFile('featured_image')) {
            if ($listing->featured_image) {
                StorageDisk::uploadsDisk()->delete($listing->featured_image);
            }
            $data['featured_image'] = $this->images->store($request->file('featured_image'));
        }

        if ($request->hasFile('floor_plan')) {
            if ($listing->floor_plan_path) {
                StorageDisk::uploadsDisk()->delete($listing->floor_plan_path);
            }
            $data['floor_plan_path'] = $request->file('floor_plan')->store('listings/floor-plans', StorageDisk::uploads());
        }

        if ($request->hasFile('private_document')) {
            if ($listing->private_document_path) {
                StorageDisk::privateDisk()->delete($listing->private_document_path);
            }
            $data['private_document_path'] = $request->file('private_document')->store('listings/private', StorageDisk::private());
        }

        $data = $this->enforceKycPendingStatus($data, $request->user(), $listing);

        $listing->update($data);

        if ($request->hasFile('images')) {
            $sortStart = (int) $listing->images()->max('sort_order') + 1;
            foreach ($request->file('images') as $i => $file) {
                $path = $this->images->store($file);
                ListingImage::create(['listing_id' => $listing->id, 'path' => $path, 'sort_order' => $sortStart + $i]);
            }
        }

        $this->listingCache->flush();

        return redirect()->route('admin.listings.index')->with('success', 'Listing updated.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $this->authorize('delete', $listing);

        foreach ($listing->images as $img) {
            StorageDisk::uploadsDisk()->delete($img->path);
        }
        if ($listing->featured_image && ! str_starts_with($listing->featured_image, 'http')) {
            StorageDisk::uploadsDisk()->delete($listing->featured_image);
        }
        if ($listing->floor_plan_path) {
            StorageDisk::uploadsDisk()->delete($listing->floor_plan_path);
        }
        if ($listing->private_document_path) {
            StorageDisk::privateDisk()->delete($listing->private_document_path);
        }
        $listing->delete();
        $this->listingCache->flush();

        return redirect()->route('admin.listings.index')->with('success', 'Listing deleted.');
    }

    public function destroyImage(Listing $listing, ListingImage $image): RedirectResponse
    {
        $this->authorize('update', $listing);
        abort_unless((int) $image->listing_id === (int) $listing->id, 404);

        StorageDisk::uploadsDisk()->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    public function reorderImages(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:listing_images,id'],
        ]);

        foreach (array_values($data['order']) as $index => $imageId) {
            ListingImage::query()
                ->where('listing_id', $listing->id)
                ->where('id', $imageId)
                ->update(['sort_order' => $index]);
        }

        return back()->with('success', 'Gallery order saved.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Listing::class);

        $user = $request->user();
        $query = Listing::query()->with(['listingType', 'city'])->forUserCountry($user);
        if (! $user->can('listings.view.any')) {
            $query->where(fn ($q) => $q->where('agent_id', $user->id)
                ->orWhere('owner_id', $user->id)
                ->orWhere('manager_id', $user->id)
                ->orWhere(function ($org) use ($user) {
                    if ($user->hasRole('organization_admin') && $user->organization_id) {
                        $org->where('organization_id', $user->organization_id);
                    } else {
                        $org->whereRaw('1 = 0');
                    }
                }));
        }

        $rows = $query->latest()->get()->map(fn (Listing $l) => [
            'id' => $l->id,
            'title' => $l->title,
            'location' => $l->location,
            'purpose' => $l->purpose,
            'price' => $l->price,
            'status' => $l->status,
            'type' => $l->listingType?->name,
            'city' => $l->city?->name,
        ]);

        return CsvExporter::download('listings.csv', ['id', 'title', 'location', 'purpose', 'price', 'status', 'type', 'city'], $rows);
    }

    public function bulk(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Listing::class);

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:listings,id'],
            'action' => ['required', 'in:delete,status'],
            'status' => ['nullable', 'in:'.implode(',', Listing::STATUSES)],
        ]);

        $listings = Listing::query()->whereIn('id', $data['ids'])->get();

        foreach ($listings as $listing) {
            if ($data['action'] === 'delete' && $request->user()->can('delete', $listing)) {
                $listing->delete();
            } elseif ($data['action'] === 'status' && ! empty($data['status']) && $request->user()->can('update', $listing)) {
                $statusData = $this->enforceKycPendingStatus(['status' => $data['status']], $request->user(), $listing);
                $listing->update(['status' => $statusData['status']]);
            }
        }

        $this->listingCache->flush();

        return back()->with('success', 'Bulk action applied to listings.');
    }

    public function downloadPrivateDocument(Listing $listing): StreamedResponse
    {
        $this->authorize('view', $listing);
        abort_unless(filled($listing->private_document_path), 404);

        return StorageDisk::privateDisk()->download($listing->private_document_path);
    }

    /**
     * Unverified agents/landlords cannot publish — force pending.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function enforceKycPendingStatus(array $data, User $actor, ?Listing $listing = null): array
    {
        $agentId = $data['agent_id'] ?? $listing?->agent_id ?? ($actor->hasRole('agent') ? $actor->id : null);
        $ownerId = $data['owner_id'] ?? $listing?->owner_id ?? ($actor->hasRole('landlord') ? $actor->id : null);

        $needsKyc = false;
        if ($agentId) {
            $agent = User::query()->find($agentId);
            $needsKyc = $agent && ! $agent->isKycVerified() && $agent->hasAnyRole(['agent', 'hotel_manager']);
        }
        if (! $needsKyc && $ownerId) {
            $owner = User::query()->find($ownerId);
            $needsKyc = $owner && ! $owner->isKycVerified() && $owner->hasRole('landlord');
        }

        if ($needsKyc && ($data['status'] ?? null) === 'published') {
            $data['status'] = 'pending';
        }

        return $data;
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
