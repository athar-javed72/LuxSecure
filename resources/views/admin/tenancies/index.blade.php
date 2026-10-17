@extends('admin.layout')

@section('title', 'Tenancies')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tenancies'],
]" />

<x-page-header title="Tenancies" subtitle="Create leases and track rent schedules">
    <x-slot:actions>
        <x-button variant="ghost" href="{{ route('admin.tenancies.overdue') }}">Overdue</x-button>
        @can('create', App\Models\Tenancy::class)
            <x-button variant="primary" href="{{ route('admin.tenancies.create') }}">New tenancy</x-button>
        @endcan
    </x-slot:actions>
</x-page-header>

@if($tenancies->isEmpty())
    <x-empty-state
        title="No tenancies yet"
        description="Create a lease to generate a monthly rent schedule."
        action-label="New tenancy"
        :action-href="route('admin.tenancies.create')"
    />
@else
    <x-table caption="Tenancies" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>Listing</th>
                    <th>Tenant</th>
                    <th>Rent</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tenancies as $tenancy)
                    <tr>
                        <td data-label="Listing" class="font-medium">{{ $tenancy->listing->title }}</td>
                        <td data-label="Tenant">{{ $tenancy->tenant->name }}</td>
                        <td data-label="Rent">{{ number_format((float) $tenancy->monthly_rent, 0) }}</td>
                        <td data-label="Period" class="text-fs-secondary">{{ $tenancy->start_date->toDateString() }} → {{ optional($tenancy->end_date)->toDateString() ?? 'open' }}</td>
                        <td data-label="Status"><x-status-badge :status="$tenancy->status" /></td>
                        <td data-label="Actions" class="text-right">
                            <x-button variant="ghost" href="{{ route('admin.tenancies.show', $tenancy) }}" class="!px-3">View</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif

<div class="mt-4">{{ $tenancies->links() }}</div>
@endsection
