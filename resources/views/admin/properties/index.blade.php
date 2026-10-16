@extends('admin.layout')

@section('title', 'Properties')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Properties'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Properties</h1>
        <p class="mt-2 text-sm text-fs-secondary max-w-prose">Manage property listings</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <x-button variant="ghost" href="{{ route('admin.dashboard') }}">Dashboard</x-button>
        <x-button variant="primary" href="{{ route('admin.properties.create') }}">Add property</x-button>
    </div>
</div>

<form method="GET" action="{{ route('admin.properties.index') }}" class="mb-6 rounded-md bg-white shadow-sm border border-black/5 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end w-full max-w-5xl">
    <x-input label="Search" name="search" :value="request('search')" placeholder="Title or location" />
    <x-select
        label="Type"
        name="type"
        :options="collect($types)->mapWithKeys(fn ($t) => [$t => $t])->all()"
        :selected="request('type')"
        placeholder="All types"
    />
    <x-select
        label="Status"
        name="status"
        :options="['active' => 'Active', 'inactive' => 'Inactive']"
        :selected="request('status')"
        placeholder="All statuses"
    />
    <div class="flex flex-wrap gap-2">
        <x-button type="submit" variant="primary">Filter</x-button>
        @if(request()->hasAny(['search', 'type', 'status']))
            <x-button variant="ghost" href="{{ route('admin.properties.index') }}">Clear</x-button>
        @endif
    </div>
</form>

<div class="rounded-md bg-white shadow-sm border border-black/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm xl:table-fixed">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th class="px-3 py-3 font-semibold xl:w-[8%]">Image</th>
                    <th class="px-3 py-3 font-semibold xl:w-[22%]">Title</th>
                    <th class="px-3 py-3 font-semibold xl:w-[18%]">Location</th>
                    <th class="px-3 py-3 font-semibold xl:w-[10%]">Type</th>
                    <th class="px-3 py-3 font-semibold xl:w-[12%]">Price</th>
                    <th class="px-3 py-3 font-semibold xl:w-[10%]">Status</th>
                    <th class="px-3 py-3 font-semibold text-right xl:w-[20%]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse($properties as $p)
                    <tr>
                        <td class="px-3 py-3">
                            @if(filled($p->primary_image_url))
                                <img src="{{ $p->primary_image_url }}" alt="{{ $p->title }}" loading="lazy" width="64" height="48" class="w-14 h-10 object-cover rounded-sm">
                            @else
                                <div class="w-14 h-10 rounded-sm bg-fs-surface" role="img" aria-label="No image for {{ $p->title }}"></div>
                            @endif
                        </td>
                        <td class="px-3 py-3 font-medium text-fs-primary xl:truncate">{{ $p->title }}</td>
                        <td class="px-3 py-3 text-fs-secondary xl:truncate">{{ $p->location }}</td>
                        <td class="px-3 py-3 text-fs-secondary">{{ $p->type }}</td>
                        <td class="px-3 py-3 text-fs-secondary whitespace-nowrap">PKR {{ number_format($p->price / 1_00_00_000, 1) }} Cr</td>
                        <td class="px-3 py-3">
                            <x-badge :tone="$p->is_active ? 'success' : 'muted'">{{ $p->is_active ? 'Active' : 'Inactive' }}</x-badge>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap justify-end gap-1">
                                <x-button variant="ghost" href="{{ route('admin.properties.show', $p) }}" class="!px-3">View</x-button>
                                <x-button variant="secondary" href="{{ route('admin.properties.edit', $p) }}" class="!px-3">Edit</x-button>
                                <x-confirm-delete
                                    :action="route('admin.properties.destroy', $p)"
                                    title="Delete this property?"
                                    message="“{{ $p->title }}” will be permanently removed."
                                >
                                    <x-slot:trigger>
                                        <x-button type="button" variant="ghost" class="!px-3 !text-fs-danger">Delete</x-button>
                                    </x-slot:trigger>
                                </x-confirm-delete>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-fs-muted">No properties found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($properties->hasPages())
    <div class="mt-4">{{ $properties->links() }}</div>
@endif
@endsection
