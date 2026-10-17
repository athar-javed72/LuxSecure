@extends('admin.layout')

@section('title', 'Cancellation Policies')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Cancellation policies'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Cancellation policies</h1>
        <p class="mt-2 text-sm text-fs-secondary">Refund rules by days before check-in</p>
    </div>
    @can('cancellation_policies.create')
        <x-button variant="primary" href="{{ route('admin.cancellation-policies.create') }}">Add policy</x-button>
    @endcan
</div>

<x-panel :padding="false">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3 font-semibold">Name</th>
                <th class="px-3 py-3 font-semibold">Scope</th>
                <th class="px-3 py-3 font-semibold">Rules</th>
                <th class="px-3 py-3 font-semibold">Default</th>
                <th class="px-3 py-3 font-semibold">Active</th>
                <th class="px-3 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse($policies as $policy)
                <tr>
                    <td class="px-3 py-3 font-medium text-fs-primary">{{ $policy->name }}</td>
                    <td class="px-3 py-3 text-fs-secondary">
                        {{ $policy->organization?->name ?? $policy->country?->name ?? 'Global' }}
                    </td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $policy->rules_count }}</td>
                    <td class="px-3 py-3"><x-badge :tone="$policy->is_default ? 'success' : 'muted'">{{ $policy->is_default ? 'Yes' : 'No' }}</x-badge></td>
                    <td class="px-3 py-3"><x-badge :tone="$policy->is_active ? 'success' : 'muted'">{{ $policy->is_active ? 'Yes' : 'No' }}</x-badge></td>
                    <td class="px-3 py-3 text-right">
                        <div class="inline-flex flex-wrap gap-2 justify-end">
                            <x-button variant="secondary" href="{{ route('admin.cancellation-policies.preview', $policy) }}" class="!px-3">Preview</x-button>
                            @can('cancellation_policies.update')
                                <x-button variant="secondary" href="{{ route('admin.cancellation-policies.edit', $policy) }}" class="!px-3">Edit</x-button>
                            @endcan
                            @can('cancellation_policies.delete')
                                <x-confirm-delete :action="route('admin.cancellation-policies.destroy', $policy)" label="Delete" />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No policies yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-panel>

@if($policies->hasPages())
    <div class="mt-4">{{ $policies->links() }}</div>
@endif
@endsection
