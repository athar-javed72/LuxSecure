@extends('admin.layout')

@section('title', 'Tenancy')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Tenancies', 'href' => route('admin.tenancies.index')],
    ['label' => $tenancy->listing->title],
]" />

<x-page-header :title="$tenancy->listing->title" :subtitle="'Tenant: '.$tenancy->tenant->name.' · Landlord: '.$tenancy->landlord->name">
    <x-slot:actions>
        @can('update', $tenancy)
            <x-button variant="ghost" href="{{ route('admin.tenancies.edit', $tenancy) }}">Edit</x-button>
        @endcan
    </x-slot:actions>
</x-page-header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <x-stat-card label="Monthly rent" :value="number_format((float) $tenancy->monthly_rent, 0)" />
    <x-stat-card label="Deposit" :value="number_format((float) $tenancy->security_deposit, 0)" />
    <x-panel class="flex flex-col justify-center">
        <p class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Status</p>
        <div class="mt-2"><x-status-badge :status="$tenancy->status" class="!text-sm !h-8 !px-3" /></div>
    </x-panel>
</div>

<x-panel :padding="false">
    <div class="px-4 py-3 border-b border-black/5 fs-display font-semibold text-fs-primary">Rent schedule</div>
    <x-table class="fs-table-stack">
            <thead class="bg-fs-surface text-left text-xs uppercase tracking-wide text-fs-muted">
                <tr>
                    <th>Due</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Receipt</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tenancy->payments as $payment)
                    <tr>
                        <td data-label="Due">{{ $payment->due_date->toDateString() }}</td>
                        <td data-label="Amount">{{ number_format((float) $payment->amount, 0) }}</td>
                        <td data-label="Status">
                            <x-status-badge :status="$payment->isOverdue() ? 'overdue' : $payment->status" />
                        </td>
                        <td data-label="Receipt">{{ $payment->receipt_no ?? '—' }}</td>
                        <td data-label="Actions" class="text-right">
                            @if ($payment->status !== 'paid' && auth()->user()->can('update', $payment))
                                <form method="POST" action="{{ route('admin.rent-payments.mark-paid', $payment) }}" class="inline-flex">
                                    @csrf
                                    <input type="hidden" name="method" value="cash">
                                    <x-button type="submit" variant="ghost" class="!px-3">Mark paid</x-button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-fs-muted">No scheduled payments.</td></tr>
                @endforelse
            </tbody>
        </x-table>
</x-panel>
@endsection
