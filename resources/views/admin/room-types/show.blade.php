@extends('admin.layout')

@section('title', $roomType->name)

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Room types', 'href' => route('admin.room-types.index')],
    ['label' => $roomType->name],
]" />

<x-page-header
    :title="$roomType->name"
    :subtitle="$roomType->listing->title.' · capacity '.$roomType->capacity.' · base '.number_format((float) $roomType->base_price, 0).'/night'"
/>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-panel class="space-y-4">
        <h2 class="fs-display text-base font-semibold text-fs-primary">Rooms</h2>
        <ul class="text-sm divide-y divide-black/5">
            @forelse ($roomType->rooms as $room)
                <li class="py-2 flex justify-between gap-2"><span>#{{ $room->number }}</span><x-status-badge :status="$room->status" /></li>
            @empty
                <li class="text-fs-muted py-2">No rooms yet.</li>
            @endforelse
        </ul>
        @can('update', $roomType)
            <form method="POST" action="{{ route('admin.rooms.store') }}" class="space-y-3 border-t border-black/5 pt-4">
                @csrf
                <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                <x-input label="Room number" name="number" required />
                <x-button type="submit" variant="primary">Add room</x-button>
            </form>
        @endcan
    </x-panel>

    <x-panel class="space-y-4">
        <h2 class="fs-display text-base font-semibold text-fs-primary">Seasonal prices</h2>
        <ul class="text-sm divide-y divide-black/5">
            @forelse ($roomType->seasonalPrices as $price)
                <li class="py-2 flex justify-between gap-2">
                    <span>{{ $price->start_date->toDateString() }} → {{ $price->end_date->toDateString() }}</span>
                    <span class="font-medium">{{ number_format((float) $price->price_per_night, 0) }}</span>
                </li>
            @empty
                <li class="text-fs-muted py-2">No seasonal rates.</li>
            @endforelse
        </ul>
        @can('update', $roomType)
            <form method="POST" action="{{ route('admin.seasonal-prices.store') }}" class="space-y-3 border-t border-black/5 pt-4">
                @csrf
                <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                <div class="grid grid-cols-2 gap-3">
                    <x-input label="Start" name="start_date" type="date" required />
                    <x-input label="End" name="end_date" type="date" required />
                </div>
                <x-input label="Price / night" name="price_per_night" type="number" step="0.01" required />
                <x-button type="submit" variant="primary">Add seasonal price</x-button>
            </form>
        @endcan
    </x-panel>
</div>
@endsection
