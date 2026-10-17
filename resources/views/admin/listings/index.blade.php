@extends('admin.layout')

@section('title', 'Listings')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Listings'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Listings</h1>
        <p class="mt-2 text-sm text-fs-secondary max-w-prose">Manage all listing inventory</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <x-button variant="ghost" href="{{ route('admin.dashboard') }}">Dashboard</x-button>
        <x-button variant="primary" href="{{ route('admin.listings.create') }}">Add listing</x-button>
    </div>
</div>

<form method="GET" action="{{ route('admin.listings.index') }}" class="mb-6 rounded-md bg-white shadow-sm border border-black/5 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end w-full max-w-5xl">
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
    <div class="flex flex-wrap gap-2">
        <x-button type="submit" variant="primary">Filter</x-button>
        @if(request()->hasAny(['search', 'listing_type_id', 'status']))
            <x-button variant="ghost" href="{{ route('admin.listings.index') }}">Clear</x-button>
        @endif
    </div>
</form>

<div class="rounded-md bg-white shadow-sm border border-black/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm xl:table-fixed">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th class="px-3 py-3 font-semibold">Title</th>
                    <th class="px-3 py-3 font-semibold">Type</th>
                    <th class="px-3 py-3 font-semibold">Purpose</th>
                    <th class="px-3 py-3 font-semibold">Price</th>
                    <th class="px-3 py-3 font-semibold">Status</th>
                    <th class="px-3 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse($listings as $listing)
                    <tr>
                        <td class="px-3 py-3 font-medium text-fs-primary">{{ $listing->title }}</td>
                        <td class="px-3 py-3 text-fs-secondary">{{ $listing->listingType?->name }}</td>
                        <td class="px-3 py-3 text-fs-secondary">{{ ucfirst(str_replace('_', ' ', $listing->purpose)) }}</td>
                        <td class="px-3 py-3 text-fs-secondary whitespace-nowrap">{{ $listing->formatted_price }}</td>
                        <td class="px-3 py-3"><x-badge :tone="$listing->status === 'published' ? 'success' : 'muted'">{{ ucfirst($listing->status) }}</x-badge></td>
                        <td class="px-3 py-3 text-right">
                            <div class="inline-flex flex-wrap gap-2 justify-end">
                                <x-button variant="ghost" href="{{ route('admin.listings.show', $listing) }}" class="!px-3">View</x-button>
                                <x-button variant="secondary" href="{{ route('admin.listings.edit', $listing) }}" class="!px-3">Edit</x-button>
                                <x-confirm-delete :action="route('admin.listings.destroy', $listing)" label="Delete" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No listings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($listings->hasPages())
    <div class="mt-4">{{ $listings->links() }}</div>
@endif
@endsection
