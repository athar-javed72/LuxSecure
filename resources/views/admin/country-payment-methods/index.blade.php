@extends('admin.layout')

@section('title', 'Country Payment Methods')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Payment methods'],
]" />

<x-page-header title="Country payment methods" subtitle="Enable gateways per country" />

<x-panel class="mb-6 max-w-md">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <x-select label="Country" name="country_id" :options="$countries->all()" :selected="$countryId" required />
        <x-button type="submit" variant="secondary">Load</x-button>
    </form>
</x-panel>

@if($countryId)
    <form method="POST" action="{{ route('admin.country-payment-methods.update') }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="country_id" value="{{ $countryId }}">
        <x-panel class="max-w-xl space-y-4">
            @foreach ($methods as $i => $method)
                <div class="flex items-center justify-between gap-4 py-2 border-b border-black/5 last:border-0">
                    <input type="hidden" name="methods[{{ $i }}][gateway]" value="{{ $method->gateway }}">
                    <span class="text-sm font-medium text-fs-primary">{{ str_replace('_', ' ', ucfirst($method->gateway)) }}</span>
                    <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
                        <input type="hidden" name="methods[{{ $i }}][is_enabled]" value="0">
                        <input type="checkbox" name="methods[{{ $i }}][is_enabled]" value="1" {{ $method->is_enabled ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
                        <span class="text-sm text-fs-secondary">Enabled</span>
                    </label>
                </div>
            @endforeach
            @can('countries.update')
                <x-button type="submit" variant="primary">Save toggles</x-button>
            @endcan
        </x-panel>
    </form>
@endif
@endsection
