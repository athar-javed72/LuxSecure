@extends('admin.layout')

@section('title', 'Currencies')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Currencies'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Currencies</h1>
        <p class="mt-2 text-sm text-fs-secondary">Exchange codes and display symbols</p>
    </div>
    @can('currencies.create')
        <x-button variant="primary" href="{{ route('admin.currencies.create') }}">Add currency</x-button>
    @endcan
</div>

<x-panel :padding="false">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3 font-semibold">Code</th>
                <th class="px-3 py-3 font-semibold">Symbol</th>
                <th class="px-3 py-3 font-semibold">Decimals</th>
                <th class="px-3 py-3 font-semibold">Rate</th>
                <th class="px-3 py-3 font-semibold">Active</th>
                <th class="px-3 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse($currencies as $currency)
                <tr>
                    <td class="px-3 py-3 font-medium text-fs-primary">{{ $currency->code }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $currency->symbol }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $currency->decimals }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $currency->rate_to_base ?? '—' }}</td>
                    <td class="px-3 py-3"><x-badge :tone="$currency->is_active ? 'success' : 'muted'">{{ $currency->is_active ? 'Yes' : 'No' }}</x-badge></td>
                    <td class="px-3 py-3 text-right">
                        <div class="inline-flex flex-wrap gap-2 justify-end">
                            @can('currencies.update')
                                <x-button variant="secondary" href="{{ route('admin.currencies.edit', $currency) }}" class="!px-3">Edit</x-button>
                            @endcan
                            @can('currencies.delete')
                                <x-confirm-delete :action="route('admin.currencies.destroy', $currency)" label="Delete" />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No currencies yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-panel>

@if($currencies->hasPages())
    <div class="mt-4">{{ $currencies->links() }}</div>
@endif
@endsection
