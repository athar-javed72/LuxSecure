@extends('include.master')

@section('title', 'My bookings')

@section('content')
<x-container class="py-8">
    <h1 class="text-2xl font-bold text-fs-primary">My bookings</h1>
    <p class="mt-2 text-sm text-fs-secondary">Your hotel stays and short-stay reservations</p>

    @if (session('success'))
        <div class="mt-4 p-4 rounded-sm bg-fs-brand-light text-fs-brand text-sm">{{ session('success') }}</div>
    @endif

    <div class="mt-6 space-y-4">
        @forelse ($bookings as $booking)
            <article class="rounded-md bg-white border border-black/5 p-5 flex flex-wrap justify-between gap-4">
                <div>
                    <h2 class="font-semibold text-fs-primary">{{ $booking->listing->title }}</h2>
                    <p class="text-sm text-fs-secondary mt-1">{{ $booking->roomType->name }} · {{ $booking->check_in->toDateString() }} → {{ $booking->check_out->toDateString() }}</p>
                    <p class="text-sm mt-2 capitalize">Status: {{ $booking->status }} · Total {{ number_format((float) $booking->total, 0) }}</p>
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
            </article>
        @empty
            <p class="text-fs-muted text-sm">You have no bookings yet.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $bookings->links() }}</div>
</x-container>
@endsection
