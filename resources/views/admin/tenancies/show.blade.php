@extends('admin.layout')

@section('title', 'Tenancy')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tenancies', 'href' => route('admin.tenancies.index')],
    ['label' => $tenancy->listing->title],
]" />

@if (session('success'))
    <div class="mb-4 p-4 rounded-sm bg-fs-brand-light text-fs-brand text-sm">{{ session('success') }}</div>
@endif

<div class="flex flex-wrap justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">{{ $tenancy->listing->title }}</h1>
        <p class="mt-2 text-sm text-fs-secondary">Tenant: {{ $tenancy->tenant->name }} · Landlord: {{ $tenancy->landlord->name }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @can('update', $tenancy)
            <x-button variant="ghost" href="{{ route('admin.tenancies.edit', $tenancy) }}">Edit</x-button>
        @endcan
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="rounded-md bg-white border border-black/5 p-4">
        <p class="text-xs uppercase text-fs-muted">Monthly rent</p>
        <p class="mt-1 text-lg font-semibold">{{ number_format((float) $tenancy->monthly_rent, 0) }}</p>
    </div>
    <div class="rounded-md bg-white border border-black/5 p-4">
        <p class="text-xs uppercase text-fs-muted">Deposit</p>
        <p class="mt-1 text-lg font-semibold">{{ number_format((float) $tenancy->security_deposit, 0) }}</p>
    </div>
    <div class="rounded-md bg-white border border-black/5 p-4">
        <p class="text-xs uppercase text-fs-muted">Status</p>
        <p class="mt-1 text-lg font-semibold capitalize">{{ $tenancy->status }}</p>
    </div>
</div>

<div class="rounded-md bg-white border border-black/5 overflow-hidden">
    <div class="px-4 py-3 border-b border-black/5 font-semibold">Rent schedule</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-fs-surface text-left text-xs uppercase text-fs-muted">
                <tr>
                    <th class="px-4 py-3">Due</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Receipt</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($tenancy->payments as $payment)
                    <tr>
                        <td class="px-4 py-3">{{ $payment->due_date->toDateString() }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $payment->amount, 0) }}</td>
                        <td class="px-4 py-3 capitalize {{ $payment->isOverdue() ? 'text-fs-danger' : '' }}">{{ $payment->status }}</td>
                        <td class="px-4 py-3">{{ $payment->receipt_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($payment->status !== 'paid' && auth()->user()->can('update', $payment))
                                <form method="POST" action="{{ route('admin.rent-payments.mark-paid', $payment) }}" class="inline-flex gap-2 items-center">
                                    @csrf
                                    <input type="hidden" name="method" value="cash">
                                    <button type="submit" class="text-fs-brand hover:underline text-sm">Mark paid</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
