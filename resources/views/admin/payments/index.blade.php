@extends('admin.layout')

@section('title', 'Payments')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Payments'],
]" />

<x-page-header title="Payments">
    <x-slot:actions>
        <x-button variant="ghost" href="{{ route('admin.payments.export', request()->query()) }}">Export CSV</x-button>
    </x-slot:actions>
</x-page-header>

<x-panel class="mb-4">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <x-input label="Search" name="search" :value="request('search')" placeholder="UUID, email…" />
        <x-select label="Status" name="status" :options="collect($statuses)->mapWithKeys(fn ($s) => [$s => ucfirst(str_replace('_', ' ', $s))])->all()" :selected="request('status')" placeholder="All" />
        <x-select label="Gateway" name="gateway" :options="collect($gateways)->mapWithKeys(fn ($g) => [$g => ucfirst(str_replace('_', ' ', $g))])->all()" :selected="request('gateway')" placeholder="All" />
        <div class="sm:col-span-4">
            <x-location-picker
                :countries="$filterCountries ?? collect()"
                country-name="country_id"
                city-name="city_id"
                :country-id="request('country_id')"
                :city-id="request('city_id')"
                :required="false"
                :allow-empty-country="true"
            />
        </div>
        <div class="flex items-end"><x-button type="submit" variant="primary">Filter</x-button></div>
    </form>
</x-panel>

@if($payments->isEmpty())
    <x-empty-state title="No payments found" description="Try adjusting your filters or check back later." />
@else
    <x-table caption="Payments" class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>UUID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Gateway</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td data-label="UUID" class="font-mono text-xs">{{ Str::limit($payment->uuid, 13) }}</td>
                        <td data-label="User">{{ $payment->user?->email }}</td>
                        <td data-label="Amount">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</td>
                        <td data-label="Gateway">{{ $payment->gateway }}</td>
                        <td data-label="Status"><x-status-badge :status="$payment->status" /></td>
                        <td data-label="Actions" class="text-right">
                            <x-button variant="ghost" href="{{ route('admin.payments.show', $payment) }}" class="!px-3">Open</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
@endif
<div class="mt-4">{{ $payments->links() }}</div>
@endsection
