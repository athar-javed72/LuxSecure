@extends('admin.layout')

@section('title', 'Booking #'.$booking->id)

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Bookings', 'href' => route('admin.bookings.index')],
    ['label' => '#'.$booking->id],
]" />

<x-page-header
    :title="'Booking #'.$booking->id"
    :subtitle="$booking->listing->title.' · '.$booking->roomType->name.($booking->room ? ' · Room '.$booking->room->number : '')"
/>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <x-panel class="space-y-2 text-sm">
        <p><span class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Guest</span><br>{{ $booking->user->name }} ({{ $booking->user->email }})</p>
        <p><span class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Dates</span><br>{{ $booking->check_in->toDateString() }} → {{ $booking->check_out->toDateString() }}</p>
        <p><span class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Guests</span><br>{{ $booking->guests }}</p>
        <p><span class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Total</span><br>{{ number_format((float) $booking->total, 2) }}</p>
        <p><span class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Policy</span><br>{{ $booking->cancellation_policy }}</p>
        <p class="flex items-center gap-2 flex-wrap"><span class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Status</span> <x-status-badge :status="$booking->status" /></p>
    </x-panel>

    @can('update', $booking)
        <x-panel>
            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="space-y-3">
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
        </x-panel>
    @endcan
</div>
@endsection
