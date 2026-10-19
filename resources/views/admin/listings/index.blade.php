@extends('admin.layout')

@section('title', 'Listings')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Listings'],
]" />

<x-page-header title="Listings" subtitle="Manage all listing inventory">
    <x-slot:actions>
        <x-button variant="ghost" href="{{ route('admin.listings.export') }}">Export CSV</x-button>
        <x-button variant="primary" href="{{ route('admin.listings.create') }}">Add listing</x-button>
    </x-slot:actions>
</x-page-header>

<x-panel class="mb-6">
    <form method="GET" action="{{ route('admin.listings.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
        <x-input label="Search" name="search" :value="request('search')" placeholder="Title or location" />
        <x-select
            label="Type"
            name="listing_type_id"
            :options="$listingTypes->mapWithKeys(fn ($t) => [$t->id => $t->name])->all()"
            :selected="request('listing_type_id')"
            placeholder="All types"
        />
        <x-select
            label="Status"
            name="status"
            :options="collect(\App\Models\Listing::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()"
            :selected="request('status')"
            placeholder="All statuses"
        />
        <div class="sm:col-span-2 lg:col-span-4">
            <x-location-picker
                :countries="$filterCountries ?? collect()"
                country-name="country_id"
                city-name="city_id"
                :country-id="request('country_id')"
                :city-id="request('city_id')"
                :required="false"
                :allow-empty-country="true"
            />
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Filter</x-button>
            @if(request()->hasAny(['search', 'listing_type_id', 'status', 'country_id', 'city_id']))
                <x-button variant="ghost" href="{{ route('admin.listings.index') }}">Clear</x-button>
            @endif
        </div>
    </form>
</x-panel>

<form method="POST" action="{{ route('admin.listings.bulk') }}">
    @csrf
    <x-panel class="mb-4">
        <div class="flex flex-wrap gap-3 items-end">
            <x-select
                label="Bulk action"
                name="action"
                :options="['status' => 'Set status', 'delete' => 'Delete']"
                :selected="'status'"
            />
            <x-select
                label="Status"
                name="status"
                :options="collect(\App\Models\Listing::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()"
                :selected="'published'"
            />
            <x-button type="submit" variant="secondary">Apply to selected</x-button>
        </div>
    </x-panel>

    @if($listings->isEmpty())
        <x-empty-state
            title="No listings found"
            description="Adjust filters or create a new listing to get started."
            action-label="Add listing"
            :action-href="route('admin.listings.create')"
        />
    @else
        <x-table caption="Listings" class="fs-table-stack">
            <thead>
                <tr>
                    <th scope="col"><span class="sr-only">Select</span></th>
                    <th scope="col">Title</th>
                    <th scope="col">Type</th>
                    <th scope="col">Purpose</th>
                    <th scope="col">Price</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listings as $listing)
                    <tr>
                        <td data-label="Select">
                            <input type="checkbox" name="ids[]" value="{{ $listing->id }}" class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand" aria-label="Select {{ $listing->title }}">
                        </td>
                        <td data-label="Title" class="font-medium text-fs-primary">{{ $listing->title }}</td>
                        <td data-label="Type" class="text-fs-secondary">{{ $listing->listingType?->name }}</td>
                        <td data-label="Purpose" class="text-fs-secondary">{{ ucfirst(str_replace('_', ' ', $listing->purpose)) }}</td>
                        <td data-label="Price" class="text-fs-secondary whitespace-nowrap">{{ $listing->formatted_price }}</td>
                        <td data-label="Status"><x-status-badge :status="$listing->status" /></td>
                        <td data-label="Actions" class="text-right">
                            <div class="inline-flex flex-wrap gap-2 justify-end">
                                <x-button variant="ghost" href="{{ route('admin.listings.show', $listing) }}" class="!px-3">View</x-button>
                                <x-button variant="secondary" href="{{ route('admin.listings.edit', $listing) }}" class="!px-3">Edit</x-button>
                                <x-confirm-delete :action="route('admin.listings.destroy', $listing)" label="Delete" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</form>

@if($listings->hasPages())
    <div class="mt-4">{{ $listings->links() }}</div>
@endif
@endsection
