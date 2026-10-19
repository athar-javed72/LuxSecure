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
    <div class="flex flex-wrap gap-2">
        @can('countries.manage_visibility')
            <x-button variant="secondary" href="{{ route('admin.countries.visibility') }}">Visibility</x-button>
        @endcan
        @can('countries.create')
            <x-button variant="primary" href="{{ route('admin.countries.create') }}">Add country</x-button>
        @endcan
    </div>
</div>

<x-panel :padding="false">
    <table class="w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3 font-semibold">Name</th>
                <th class="px-3 py-3 font-semibold">ISO2</th>
                <th class="px-3 py-3 font-semibold">ISO3</th>
                <th class="px-3 py-3 font-semibold">Currency</th>
                <th class="px-3 py-3 font-semibold">Locale</th>
                <th class="px-3 py-3 font-semibold">Status</th>
                <th class="px-3 py-3 font-semibold">Readiness</th>
                <th class="px-3 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse($countries as $country)
                @php $ready = $country->isReady(); @endphp
                <tr>
                    <td class="px-3 py-3 font-medium text-fs-primary">{{ $country->name }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $country->iso2 }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $country->iso3 ?: '—' }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $country->currency_code ?: '—' }}</td>
                    <td class="px-3 py-3 text-fs-secondary">{{ $country->default_locale ?: '—' }}</td>
                    <td class="px-3 py-3">
                        @if($country->wind_down)
                            <x-badge tone="warning">Wind-down</x-badge>
                        @elseif($country->is_active)
                            <x-badge tone="success">Active</x-badge>
                        @else
                            <x-badge tone="muted">Inactive</x-badge>
                        @endif
                    </td>
                    <td class="px-3 py-3">
                        <x-badge :tone="$ready ? 'success' : 'warning'">{{ $ready ? 'Ready' : 'Incomplete' }}</x-badge>
                    </td>
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
                <tr><td colspan="8" class="px-4 py-8 text-center text-fs-muted">No countries yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-panel>

@if($countries->hasPages())
    <div class="mt-4">{{ $countries->links() }}</div>
@endif
@endsection
