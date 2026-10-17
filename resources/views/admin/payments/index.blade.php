@extends('admin.layout')

@section('title', 'Payments')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Payments'],
]" />

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="fs-display text-2xl text-fs-primary">Payments</h1>
    <x-button variant="ghost" href="{{ route('admin.payments.export', request()->query()) }}">Export CSV</x-button>
</div>

<form method="GET" class="mb-4 grid grid-cols-1 sm:grid-cols-4 gap-3 rounded-md bg-white border border-black/5 p-4">
    <x-input label="Search" name="search" :value="request('search')" placeholder="UUID, email…" />
    <x-select label="Status" name="status" :options="collect($statuses)->mapWithKeys(fn ($s) => [$s => ucfirst(str_replace('_', ' ', $s))])->all()" :selected="request('status')" placeholder="All" />
    <x-select label="Gateway" name="gateway" :options="collect($gateways)->mapWithKeys(fn ($g) => [$g => ucfirst(str_replace('_', ' ', $g))])->all()" :selected="request('gateway')" placeholder="All" />
    <div class="flex items-end"><x-button type="submit" variant="primary">Filter</x-button></div>
</form>

<div class="overflow-x-auto rounded-md bg-white border border-black/5">
    <table class="min-w-full text-sm">
        <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
            <tr>
                <th class="px-3 py-3">UUID</th>
                <th class="px-3 py-3">User</th>
                <th class="px-3 py-3">Amount</th>
                <th class="px-3 py-3">Gateway</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr class="border-t border-black/5">
                    <td class="px-3 py-3 font-mono text-xs">{{ Str::limit($payment->uuid, 13) }}</td>
                    <td class="px-3 py-3">{{ $payment->user?->email }}</td>
                    <td class="px-3 py-3">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</td>
                    <td class="px-3 py-3">{{ $payment->gateway }}</td>
                    <td class="px-3 py-3">{{ $payment->status }}</td>
                    <td class="px-3 py-3 text-right"><a href="{{ route('admin.payments.show', $payment) }}" class="text-fs-brand hover:underline">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-3 py-8 text-center text-fs-muted">No payments found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $payments->links() }}</div>
@endsection
