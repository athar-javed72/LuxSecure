<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListingTypeRequest;
use App\Http\Requests\UpdateListingTypeRequest;
use App\Models\ListingType;
use App\Models\TypeAttribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ListingTypeController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ListingType::class);

        $listingTypes = ListingType::withCount(['attributes', 'listings'])->orderBy('name')->paginate(20);

        return view('admin.listing-types.index', compact('listingTypes'));
    }

    public function create(): View
    {
        $this->authorize('create', ListingType::class);

        return view('admin.listing-types.create');
    }

    public function store(StoreListingTypeRequest $request): RedirectResponse
    {
        $this->authorize('create', ListingType::class);

        $type = ListingType::create([
            'name' => $request->input('name'),
            'slug' => $request->input('slug') ?: Str::slug($request->input('name')),
            'icon' => $request->input('icon'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->syncAttributes($type, $request->input('attributes', []));

        return redirect()->route('admin.listing-types.index')->with('success', 'Listing type created.');
    }

    public function edit(ListingType $listingType): View
    {
        $this->authorize('update', $listingType);
        $listingType->load('attributes');

        return view('admin.listing-types.edit', compact('listingType'));
    }

    public function update(UpdateListingTypeRequest $request, ListingType $listingType): RedirectResponse
    {
        $this->authorize('update', $listingType);

        $listingType->update([
            'name' => $request->input('name'),
            'slug' => $request->input('slug') ?: Str::slug($request->input('name')),
            'icon' => $request->input('icon'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->syncAttributes($listingType, $request->input('attributes', []));

        return redirect()->route('admin.listing-types.index')->with('success', 'Listing type updated.');
    }

    public function destroy(ListingType $listingType): RedirectResponse
    {
        $this->authorize('delete', $listingType);

        if ($listingType->listings()->exists()) {
            return back()->with('error', 'Cannot delete a listing type that still has listings.');
        }

        $listingType->delete();

        return redirect()->route('admin.listing-types.index')->with('success', 'Listing type deleted.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function syncAttributes(ListingType $type, array $rows): void
    {
        $keepIds = [];

        foreach (array_values($rows) as $index => $row) {
            if (blank($row['key'] ?? null) || blank($row['label'] ?? null)) {
                continue;
            }

            $options = null;
            if (! empty($row['options'])) {
                $parts = array_values(array_filter(array_map('trim', explode(',', (string) $row['options']))));
                $options = $parts;
            }

            $payload = [
                'listing_type_id' => $type->id,
                'key' => $row['key'],
                'label' => $row['label'],
                'input_type' => $row['input_type'] ?? 'text',
                'options' => $options,
                'is_required' => filter_var($row['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'is_filterable' => filter_var($row['is_filterable'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'sort_order' => (int) ($row['sort_order'] ?? $index),
            ];

            if (! empty($row['id'])) {
                $attr = TypeAttribute::query()
                    ->where('listing_type_id', $type->id)
                    ->where('id', $row['id'])
                    ->first();
                if ($attr) {
                    $attr->update($payload);
                    $keepIds[] = $attr->id;

                    continue;
                }
            }

            $attr = TypeAttribute::create($payload);
            $keepIds[] = $attr->id;
        }

        TypeAttribute::query()
            ->where('listing_type_id', $type->id)
            ->whereNotIn('id', $keepIds !== [] ? $keepIds : [0])
            ->delete();
    }
}
