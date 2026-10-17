@extends('include.master')

@section('title', 'My bookings')

@section('content')
<x-container class="py-8">
    <x-page-header
        title="My bookings"
        subtitle="Your hotel stays and short-stay reservations"
    />

    <div class="space-y-4">
        @forelse ($bookings as $booking)
            <x-panel class="flex flex-wrap justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-semibold text-fs-primary">{{ $booking->listing->title }}</h2>
                        <x-status-badge :status="$booking->status" />
                    </div>
                    <p class="text-sm text-fs-secondary mt-1">{{ $booking->roomType->name }} · {{ $booking->check_in->toDateString() }} → {{ $booking->check_out->toDateString() }}</p>
                    <p class="text-sm mt-2 text-fs-secondary">Total {{ number_format((float) $booking->total, 0) }}</p>
                </div>
                <div class="flex flex-wrap gap-2 items-start">
                    <x-button variant="ghost" href="{{ route('bookings.show', $booking) }}">Details</x-button>
                    @if (in_array($booking->status, ['pending', 'confirmed'], true))
                        <form method="POST" action="{{ route('bookings.cancel', $booking) }}">
                            @csrf
                            <x-button type="submit" variant="ghost">Cancel</x-button>
                        </form>
                    @endif
                </div>
            </x-panel>
        @empty
            <x-empty-state
                title="No bookings yet"
                description="When you reserve a stay, it will appear here with status and payment options."
                action-label="Browse properties"
                :action-href="route('properties')"
            />
        @endforelse
    </div>

    <div class="mt-6">{{ $bookings->links() }}</div>
</x-container>
@endsection
