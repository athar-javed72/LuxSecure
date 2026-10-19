@extends('admin.layout')

@section('title', 'Country visibility')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Countries', 'href' => route('admin.countries.index')],
    ['label' => 'Visibility'],
]" />

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Country visibility</h1>
        <p class="mt-2 text-sm text-fs-secondary">Enable countries on the website only when readiness is complete. Disabling never deletes data.</p>
    </div>
    <x-button variant="secondary" href="{{ route('admin.countries.index') }}">Back to list</x-button>
</div>

<form method="get" action="{{ route('admin.countries.visibility') }}" class="mb-4 flex flex-wrap gap-2 items-end">
    <div class="min-w-[220px] flex-1">
        <label for="q" class="block text-xs font-semibold text-fs-primary mb-1">Search</label>
        <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Name, ISO2, ISO3"
               class="w-full h-11 px-3 rounded-sm border border-slate-200 text-sm">
    </div>
    <x-button type="submit" variant="secondary">Search</x-button>
</form>

@if($errors->any())
    <div class="mb-4 rounded-sm border border-fs-danger/30 bg-red-50 px-4 py-3 text-sm text-fs-danger">
        <ul class="list-disc pl-4 space-y-1">
            @foreach($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="post" action="{{ route('admin.countries.visibility.update') }}">
    @csrf
    @method('PUT')

    <x-panel :padding="false">
        <table class="w-full text-sm">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th class="px-3 py-3 font-semibold w-12">On</th>
                    <th class="px-3 py-3 font-semibold">Name</th>
                    <th class="px-3 py-3 font-semibold">ISO2</th>
                    <th class="px-3 py-3 font-semibold">ISO3</th>
                    <th class="px-3 py-3 font-semibold">Status</th>
                    <th class="px-3 py-3 font-semibold">Readiness</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse($rows as $row)
                    @php
                        $country = $row['model'];
                        $ready = $row['readiness']['ready'];
                        $checked = old('enabled') !== null
                            ? in_array($country->id, array_map('intval', (array) old('enabled', [])), true)
                            : ($country->is_active && ! $country->wind_down);
                    @endphp
                    <tr>
                        <td class="px-3 py-3">
                            <input
                                type="checkbox"
                                name="enabled[]"
                                value="{{ $country->id }}"
                                class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"
                                @checked($checked)
                                @disabled(! $ready && ! $checked)
                                title="{{ $ready ? 'Ready to enable' : 'Complete readiness before enabling' }}"
                            >
                        </td>
                        <td class="px-3 py-3 font-medium text-fs-primary">{{ $country->name }}</td>
                        <td class="px-3 py-3 text-fs-secondary">{{ $country->iso2 }}</td>
                        <td class="px-3 py-3 text-fs-secondary">{{ $country->iso3 ?: '—' }}</td>
                        <td class="px-3 py-3">
                            @if($country->wind_down)
                                <x-badge tone="warning">Wind-down</x-badge>
                            @elseif($country->is_active)
                                <x-badge tone="success">Active</x-badge>
                            @else
                                <x-badge tone="muted">Inactive</x-badge>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-fs-secondary">
                            @if($ready)
                                <x-badge tone="success">Ready</x-badge>
                            @else
                                <details class="text-xs">
                                    <summary class="cursor-pointer text-fs-danger">Missing {{ count($row['readiness']['missing']) }}</summary>
                                    <ul class="mt-1 list-disc pl-4">
                                        @foreach($row['readiness']['missing'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endif
                            @if($row['blockers']['blocked'])
                                <p class="mt-1 text-xs text-fs-muted">Disable blocked: {{ implode('; ', $row['blockers']['reasons']) }}</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-fs-muted">No countries match.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-panel>

    <div class="mt-4 flex flex-wrap items-center gap-4">
        <label class="inline-flex items-center gap-2 text-sm text-fs-secondary">
            <input type="checkbox" name="confirm_wind_down" value="1" class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand" @checked(old('confirm_wind_down'))>
            Confirm wind-down for countries with active bookings / tenancies / unpaid payments
        </label>
        <x-button type="submit" variant="primary">Save changes</x-button>
    </div>
</form>
@endsection
