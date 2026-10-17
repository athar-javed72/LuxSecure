@extends('admin.layout')

@section('title', 'Organization report')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Organizations', 'href' => route('admin.organizations.index')],
    ['label' => $organization->name],
    ['label' => 'Report'],
]" />

<x-page-header
    :title="$organization->name.' — report'"
    subtitle="Combined bookings and payments across all branches."
/>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <x-stat-card label="Branches" :value="$stats['branches']" />
    <x-stat-card label="Bookings" :value="$stats['bookings_total']" />
    <x-stat-card label="Confirmed" :value="$stats['bookings_confirmed']" />
    <x-stat-card label="Paid revenue" :value="\App\Support\Money::format($stats['revenue_paid'], $organization->country?->currency_code)" />
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <x-panel>
        <p class="text-xs uppercase tracking-wide text-fs-muted">Pending bookings</p>
        <p class="mt-1 fs-display text-2xl text-fs-primary">{{ $stats['bookings_pending'] }}</p>
    </x-panel>
    <x-panel>
        <p class="text-xs uppercase tracking-wide text-fs-muted">Cancelled</p>
        <p class="mt-1 fs-display text-2xl text-fs-primary">{{ $stats['bookings_cancelled'] }}</p>
    </x-panel>
    <x-panel>
        <p class="text-xs uppercase tracking-wide text-fs-muted">Awaiting / pending pay</p>
        <p class="mt-1 fs-display text-2xl text-fs-brand">{{ \App\Support\Money::format($stats['revenue_pending'], $organization->country?->currency_code) }}</p>
    </x-panel>
</div>

<x-panel>
    <h2 class="fs-display text-lg text-fs-primary mb-4">Per branch</h2>
    @if($byBranch->isEmpty())
        <x-empty-state title="No branches" description="Add hotel listings to this organization." />
    @else
        <x-table>
            <x-slot:head>
                <tr>
                    <th>Branch</th>
                    <th>City / location</th>
                    <th>Bookings</th>
                    <th>Confirmed</th>
                </tr>
            </x-slot:head>
            @foreach($byBranch as $branch)
                <tr>
                    <td data-label="Branch">
                        <a href="{{ route('properties.show', $branch) }}" class="text-fs-brand hover:underline">{{ $branch->branch_name ?: $branch->title }}</a>
                    </td>
                    <td data-label="Location">{{ $branch->location }}</td>
                    <td data-label="Bookings">{{ $branch->bookings_count }}</td>
                    <td data-label="Confirmed">{{ $branch->confirmed_bookings_count }}</td>
                </tr>
            @endforeach
        </x-table>
    @endif
</x-panel>
@endsection
