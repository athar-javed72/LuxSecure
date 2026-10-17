@extends('admin.layout')

@section('title', 'Tenancies')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tenancies'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Tenancies</h1>
        <p class="mt-2 text-sm text-fs-secondary">Create leases and track rent schedules</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <x-button variant="ghost" href="{{ route('admin.tenancies.overdue') }}">Overdue</x-button>
        @can('create', App\Models\Tenancy::class)
            <x-button variant="primary" href="{{ route('admin.tenancies.create') }}">New tenancy</x-button>
        @endcan
    </div>
</div>

@if (session('success'))
    <div class="mb-4 p-4 rounded-sm bg-fs-brand-light text-fs-brand text-sm">{{ session('success') }}</div>
@endif

<div class="rounded-md bg-white shadow-sm border border-black/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th class="px-4 py-3">Listing</th>
                    <th class="px-4 py-3">Tenant</th>
                    <th class="px-4 py-3">Rent</th>
                    <th class="px-4 py-3">Period</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse ($tenancies as $tenancy)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $tenancy->listing->title }}</td>
                        <td class="px-4 py-3">{{ $tenancy->tenant->name }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $tenancy->monthly_rent, 0) }}</td>
                        <td class="px-4 py-3 text-fs-secondary">{{ $tenancy->start_date->toDateString() }} → {{ optional($tenancy->end_date)->toDateString() ?? 'open' }}</td>
                        <td class="px-4 py-3 capitalize">{{ $tenancy->status }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.tenancies.show', $tenancy) }}" class="text-fs-brand hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-fs-muted">No tenancies yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $tenancies->links() }}</div>
@endsection
