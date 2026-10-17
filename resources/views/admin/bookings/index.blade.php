@extends('admin.layout')

@section('title', 'Bookings')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Bookings'],
]" />

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Hotel bookings</h1>
    <p class="mt-2 text-sm text-fs-secondary">Manage stays for your hotels</p>
</div>

@if (session('success'))
    <div class="mb-4 p-4 rounded-sm bg-fs-brand-light text-fs-brand text-sm">{{ session('success') }}</div>
@endif

<div class="rounded-md bg-white shadow-sm border border-black/5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase text-fs-muted">
            <tr>
                <th class="px-4 py-3">Guest</th>
                <th class="px-4 py-3">Hotel</th>
                <th class="px-4 py-3">Dates</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($bookings as $booking)
                <tr>
                    <td class="px-4 py-3">{{ $booking->user->name }}</td>
                    <td class="px-4 py-3">{{ $booking->listing->title }} · {{ $booking->roomType->name }}</td>
                    <td class="px-4 py-3 text-fs-secondary">{{ $booking->check_in->toDateString() }} → {{ $booking->check_out->toDateString() }}</td>
                    <td class="px-4 py-3">{{ number_format((float) $booking->total, 0) }}</td>
                    <td class="px-4 py-3 capitalize">{{ $booking->status }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-fs-brand hover:underline">Open</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No bookings.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $bookings->links() }}</div>
@endsection
