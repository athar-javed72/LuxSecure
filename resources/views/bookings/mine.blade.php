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
            <x-panel class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="fs-display text-lg text-fs-primary">{{ $booking->listing->title }}</h2>
                        <x-status-badge :status="$booking->status" />
                    </div>
                    <p class="text-sm text-fs-secondary mt-1">
                        {{ $booking->roomType->name }} · {{ $booking->check_in->toDateString() }} → {{ $booking->check_out->toDateString() }}
                    </p>
                    <p class="text-sm mt-2 font-semibold text-fs-brand">{{ number_format((float) $booking->total, 0) }} PKR</p>
                    @if($booking->status === 'pending' && $booking->hold_expires_at)
                        <p class="mt-2 text-xs text-fs-secondary">
                            Hold until {{ $booking->hold_expires_at->timezone(config('app.timezone'))->format('M j, g:i A') }}
                        </p>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2 items-start shrink-0">
                    <x-button variant="primary" href="{{ route('bookings.show', $booking) }}">
                        {{ in_array($booking->status, ['pending', 'confirmed'], true) ? 'Pay / details' : 'Details' }}
                    </x-button>
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

    @if($bookings->hasPages())
        <div class="mt-6">{{ $bookings->links() }}</div>
    @endif
</x-container>
@endsection
