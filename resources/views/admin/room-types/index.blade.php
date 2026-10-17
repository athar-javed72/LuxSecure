@extends('admin.layout')

@section('title', 'Room types')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Room types'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Room types</h1>
        <p class="mt-2 text-sm text-fs-secondary">Hotel inventory for your listings</p>
    </div>
    @can('create', App\Models\RoomType::class)
        <x-button variant="primary" href="{{ route('admin.room-types.create') }}">Add room type</x-button>
    @endcan
</div>

@if (session('success'))
    <div class="mb-4 p-4 rounded-sm bg-fs-brand-light text-fs-brand text-sm">{{ session('success') }}</div>
@endif

<div class="rounded-md bg-white shadow-sm border border-black/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase text-fs-muted">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Listing</th>
                <th class="px-4 py-3">Capacity</th>
                <th class="px-4 py-3">Base price</th>
                <th class="px-4 py-3">Rooms</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($roomTypes as $type)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $type->name }}</td>
                    <td class="px-4 py-3">{{ $type->listing->title }}</td>
                    <td class="px-4 py-3">{{ $type->capacity }}</td>
                    <td class="px-4 py-3">{{ number_format((float) $type->base_price, 0) }}</td>
                    <td class="px-4 py-3">{{ $type->rooms_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.room-types.show', $type) }}" class="text-fs-brand hover:underline">Manage</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No room types yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $roomTypes->links() }}</div>
@endsection
