@extends('admin.layout')

@section('title', 'Listing Types')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Listing Types'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Listing types</h1>
        <p class="mt-2 text-sm text-fs-secondary">Define types and their dynamic attributes</p>
    </div>
    <x-button variant="primary" href="{{ route('admin.listing-types.create') }}">Add type</x-button>
</div>

@if(session('error'))
    <div class="mb-4 p-4 rounded-sm bg-red-50 text-fs-danger text-sm">{{ session('error') }}</div>
@endif

<div class="rounded-md bg-white shadow-sm border border-black/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3 font-semibold">Name</th>
                <th class="px-3 py-3 font-semibold">Slug</th>
                <th class="px-3 py-3 font-semibold">Attributes</th>
                <th class="px-3 py-3 font-semibold">Listings</th>
                <th class="px-3 py-3 font-semibold">Active</th>
                <th class="px-3 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse($listingTypes as $type)
                <tr>
                    <td class="px-3 py-3 font-medium text-fs-primary">{{ $type->name }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $type->slug }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $type->attributes_count }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $type->listings_count }}</td>
                    <td class="px-3 py-3"><x-badge :tone="$type->is_active ? 'success' : 'muted'">{{ $type->is_active ? 'Yes' : 'No' }}</x-badge></td>
                    <td class="px-3 py-3 text-right">
                        <div class="inline-flex flex-wrap gap-2 justify-end">
                            <x-button variant="secondary" href="{{ route('admin.listing-types.edit', $type) }}" class="!px-3">Edit</x-button>
                            <x-confirm-delete :action="route('admin.listing-types.destroy', $type)" label="Delete" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No listing types yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($listingTypes->hasPages())
    <div class="mt-4">{{ $listingTypes->links() }}</div>
@endif
@endsection
