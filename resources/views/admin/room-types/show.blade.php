@extends('admin.layout')

@section('title', $roomType->name)

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Room types', 'href' => route('admin.room-types.index')],
    ['label' => $roomType->name],
]" />

@if (session('success'))
    <div class="mb-4 p-4 rounded-sm bg-fs-brand-light text-fs-brand text-sm">{{ session('success') }}</div>
@endif

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">{{ $roomType->name }}</h1>
    <p class="mt-2 text-sm text-fs-secondary">{{ $roomType->listing->title }} · capacity {{ $roomType->capacity }} · base {{ number_format((float) $roomType->base_price, 0) }}/night</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="rounded-md bg-white border border-black/5 p-4 space-y-4">
        <h2 class="font-semibold">Rooms</h2>
        <ul class="text-sm divide-y divide-black/5">
            @forelse ($roomType->rooms as $room)
                <li class="py-2 flex justify-between"><span>#{{ $room->number }}</span><span class="capitalize text-fs-secondary">{{ $room->status }}</span></li>
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
    </div>

    <div class="rounded-md bg-white border border-black/5 p-4 space-y-4">
        <h2 class="font-semibold">Seasonal prices</h2>
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
    </div>
</div>
@endsection
