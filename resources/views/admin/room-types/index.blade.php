@extends('admin.layout')

@section('title', 'Room types')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Room types'],
]" />

<x-page-header title="Room types" subtitle="Hotel inventory for your listings">
    <x-slot:actions>
        @can('create', App\Models\RoomType::class)
            <x-button variant="primary" href="{{ route('admin.room-types.create') }}">Add room type</x-button>
        @endcan
    </x-slot:actions>
</x-page-header>

@if($roomTypes->isEmpty())
    <x-empty-state title="No room types yet" action-label="Add room type" :action-href="route('admin.room-types.create')" />
@else
    <x-table caption="Room types" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>Name</th>
                    <th>Listing</th>
                    <th>Capacity</th>
                    <th>Base price</th>
                    <th>Rooms</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roomTypes as $type)
                    <tr>
                        <td data-label="Name" class="font-medium">{{ $type->name }}</td>
                        <td data-label="Listing">{{ $type->listing->title }}</td>
                        <td data-label="Capacity">{{ $type->capacity }}</td>
                        <td data-label="Base price">{{ number_format((float) $type->base_price, 0) }}</td>
                        <td data-label="Rooms">{{ $type->rooms_count }}</td>
                        <td data-label="Actions" class="text-right">
                            <x-button variant="ghost" href="{{ route('admin.room-types.show', $type) }}" class="!px-3">Manage</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif
<div class="mt-4">{{ $roomTypes->links() }}</div>
@endsection
