@extends('admin.layout')

@section('title', 'KYC Document Types')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'KYC document types'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">KYC document types</h1>
        <p class="mt-2 text-sm text-fs-secondary">Required documents per country</p>
    </div>
    @can('countries.update')
        <x-button variant="primary" href="{{ route('admin.kyc-document-types.create', array_filter(['country_id' => $countryId])) }}">Add type</x-button>
    @endcan
</div>

<x-panel class="mb-6 max-w-md">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <x-select label="Country" name="country_id" :options="$countries->all()" :selected="$countryId" placeholder="All countries" />
        <x-button type="submit" variant="secondary">Filter</x-button>
    </form>
</x-panel>

<x-panel :padding="false">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3 font-semibold">Name</th>
                <th class="px-3 py-3 font-semibold">Country</th>
                <th class="px-3 py-3 font-semibold">Code</th>
                <th class="px-3 py-3 font-semibold">Required</th>
                <th class="px-3 py-3 font-semibold">Active</th>
                <th class="px-3 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse($types as $type)
                <tr>
                    <td class="px-3 py-3 font-medium text-fs-primary">{{ $type->name }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $type->country?->name }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $type->code ?? '—' }}</td>
                    <td class="px-3 py-3"><x-badge :tone="$type->is_required ? 'success' : 'muted'">{{ $type->is_required ? 'Yes' : 'No' }}</x-badge></td>
                    <td class="px-3 py-3"><x-badge :tone="$type->is_active ? 'success' : 'muted'">{{ $type->is_active ? 'Yes' : 'No' }}</x-badge></td>
                    <td class="px-3 py-3 text-right">
                        <div class="inline-flex flex-wrap gap-2 justify-end">
                            @can('countries.update')
                                <x-button variant="secondary" href="{{ route('admin.kyc-document-types.edit', $type) }}" class="!px-3">Edit</x-button>
                                <x-confirm-delete :action="route('admin.kyc-document-types.destroy', $type)" label="Delete" />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No document types yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-panel>

@if($types->hasPages())
    <div class="mt-4">{{ $types->links() }}</div>
@endif
@endsection
