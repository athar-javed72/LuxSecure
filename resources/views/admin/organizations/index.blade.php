@extends('admin.layout')

@section('title', 'Organizations')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Organizations'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Organizations</h1>
        <p class="mt-2 text-sm text-fs-secondary">Hotel groups and property operators</p>
    </div>
    @can('organizations.create')
        <x-button variant="primary" href="{{ route('admin.organizations.create') }}">Add organization</x-button>
    @endcan
</div>

<x-panel :padding="false">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3 font-semibold">Name</th>
                <th class="px-3 py-3 font-semibold">Country</th>
                <th class="px-3 py-3 font-semibold">Owner</th>
                <th class="px-3 py-3 font-semibold">Active</th>
                <th class="px-3 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse($organizations as $org)
                <tr>
                    <td class="px-3 py-3 font-medium text-fs-primary">{{ $org->name }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $org->country?->name ?? '—' }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $org->owner?->name ?? '—' }}</td>
                    <td class="px-3 py-3"><x-badge :tone="$org->is_active ? 'success' : 'muted'">{{ $org->is_active ? 'Yes' : 'No' }}</x-badge></td>
                    <td class="px-3 py-3 text-right">
                        <div class="inline-flex flex-wrap gap-2 justify-end">
                            @can('organizations.view')
                                <x-button variant="ghost" href="{{ route('admin.organizations.report', $org) }}" class="!px-3">Report</x-button>
                            @endcan
                            @can('organizations.update')
                                <x-button variant="secondary" href="{{ route('admin.organizations.edit', $org) }}" class="!px-3">Edit</x-button>
                            @endcan
                            @can('organizations.delete')
                                <x-confirm-delete :action="route('admin.organizations.destroy', $org)" label="Delete" />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-fs-muted">No organizations yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-panel>

@if($organizations->hasPages())
    <div class="mt-4">{{ $organizations->links() }}</div>
@endif
@endsection
