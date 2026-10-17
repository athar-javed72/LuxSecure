@extends('admin.layout')

@section('title', 'Booking #'.$booking->id)

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Bookings', 'href' => route('admin.bookings.index')],
    ['label' => '#'.$booking->id],
]" />

@if (session('success'))
    <div class="mb-4 p-4 rounded-sm bg-fs-brand-light text-fs-brand text-sm">{{ session('success') }}</div>
@endif

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Booking #{{ $booking->id }}</h1>
    <p class="mt-2 text-sm text-fs-secondary">{{ $booking->listing->title }} · {{ $booking->roomType->name }} @if($booking->room) · Room {{ $booking->room->number }} @endif</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="rounded-md bg-white border border-black/5 p-4 space-y-2 text-sm">
        <p><span class="text-fs-muted">Guest:</span> {{ $booking->user->name }} ({{ $booking->user->email }})</p>
        <p><span class="text-fs-muted">Dates:</span> {{ $booking->check_in->toDateString() }} → {{ $booking->check_out->toDateString() }}</p>
        <p><span class="text-fs-muted">Guests:</span> {{ $booking->guests }}</p>
        <p><span class="text-fs-muted">Total:</span> {{ number_format((float) $booking->total, 2) }}</p>
        <p><span class="text-fs-muted">Policy:</span> {{ $booking->cancellation_policy }}</p>
        <p><span class="text-fs-muted">Status:</span> <span class="capitalize font-medium">{{ $booking->status }}</span></p>
    </div>

    @can('update', $booking)
        <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="rounded-md bg-white border border-black/5 p-4 space-y-3">
            @csrf
            @method('PATCH')
            <x-select
                label="Update status"
                name="status"
                :options="collect(App\Models\Booking::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()"
                :selected="old('status', $booking->status)"
            />
            <x-button type="submit" variant="primary">Save status</x-button>
        </form>
    @endcan
</div>
@endsection
