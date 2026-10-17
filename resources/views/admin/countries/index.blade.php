@extends('admin.layout')

@section('title', 'Countries')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Countries'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Countries</h1>
        <p class="mt-2 text-sm text-fs-secondary">Multi-country platform configuration</p>
    </div>
    @can('countries.create')
        <x-button variant="primary" href="{{ route('admin.countries.create') }}">Add country</x-button>
    @endcan
</div>

<x-panel :padding="false">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3 font-semibold">Name</th>
                <th class="px-3 py-3 font-semibold">ISO</th>
                <th class="px-3 py-3 font-semibold">Currency</th>
                <th class="px-3 py-3 font-semibold">Locale</th>
                <th class="px-3 py-3 font-semibold">Tax</th>
                <th class="px-3 py-3 font-semibold">Active</th>
                <th class="px-3 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse($countries as $country)
                <tr>
                    <td class="px-3 py-3 font-medium text-fs-primary">{{ $country->name }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $country->iso2 }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $country->currency_code }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $country->default_locale }}</td>
                    <td class="px-3 py-3">
                        <span class="text-fs-secondary">{{ $country->tax_name }} {{ $country->tax_percent }}%</span>
                        @if($country->tax_needs_review)
                            <x-badge tone="warning" class="ml-1">Needs review</x-badge>
                        @endif
                    </td>
                    <td class="px-3 py-3"><x-badge :tone="$country->is_active ? 'success' : 'muted'">{{ $country->is_active ? 'Yes' : 'No' }}</x-badge></td>
                    <td class="px-3 py-3 text-right">
                        <div class="inline-flex flex-wrap gap-2 justify-end">
                            <x-button variant="secondary" href="{{ route('admin.countries.settings', $country) }}" class="!px-3">Settings</x-button>
                            @can('countries.update')
                                <x-button variant="secondary" href="{{ route('admin.countries.edit', $country) }}" class="!px-3">Edit</x-button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-fs-muted">No countries yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-panel>

@if($countries->hasPages())
    <div class="mt-4">{{ $countries->links() }}</div>
@endif
@endsection
