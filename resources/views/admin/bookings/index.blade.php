@extends('admin.layout')

@section('title', 'Bookings')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Bookings'],
]" />

<x-page-header title="Hotel bookings" subtitle="Manage stays for your hotels" />

@if($bookings->isEmpty())
    <x-empty-state title="No bookings" description="Hotel reservations will appear here when guests book." />
@else
    <x-table caption="Bookings" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>Guest</th>
                    <th>Hotel</th>
                    <th>Dates</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $booking)
                    <tr>
                        <td data-label="Guest">{{ $booking->user->name }}</td>
                        <td data-label="Hotel">{{ $booking->listing->title }} · {{ $booking->roomType->name }}</td>
                        <td data-label="Dates" class="text-fs-secondary">{{ $booking->check_in->toDateString() }} → {{ $booking->check_out->toDateString() }}</td>
                        <td data-label="Total">{{ number_format((float) $booking->total, 0) }}</td>
                        <td data-label="Status"><x-status-badge :status="$booking->status" /></td>
                        <td data-label="Actions" class="text-right">
                            <x-button variant="ghost" href="{{ route('admin.bookings.show', $booking) }}" class="!px-3">Open</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif
<div class="mt-4">{{ $bookings->links() }}</div>
@endsection
