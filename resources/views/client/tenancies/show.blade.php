@extends('include.master')

@section('title', 'Tenancy details')

@section('content')
<x-container class="py-8 max-w-3xl">
    <x-breadcrumbs :items="[
        ['label' => 'My tenancy', 'href' => route('tenancies.mine')],
        ['label' => $tenancy->listing->title],
    ]" />

    <x-page-header
        :title="$tenancy->listing->title"
        :subtitle="'Landlord: '.$tenancy->landlord->name"
    >
        <x-slot:actions>
            <x-status-badge :status="$tenancy->status" />
        </x-slot:actions>
    </x-page-header>

    <h2 class="fs-display text-xl text-fs-primary mb-3">Payment history</h2>
    <x-table caption="Rent payment history" class="fs-table-stack">
        <thead>
            <tr>
                <th scope="col">Due</th>
                <th scope="col">Amount</th>
                <th scope="col">Status</th>
                <th scope="col">Paid</th>
                <th scope="col">Receipt</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tenancy->payments as $payment)
                <tr>
                    <td data-label="Due">{{ $payment->due_date->toDateString() }}</td>
                    <td data-label="Amount">{{ number_format((float) $payment->amount, 0) }}</td>
                    <td data-label="Status"><x-status-badge :status="$payment->status" /></td>
                    <td data-label="Paid">{{ optional($payment->paid_at)->toDateString() ?? '—' }}</td>
                    <td data-label="Receipt">{{ $payment->receipt_no ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="!border-0 !px-0 !py-0">
                        <x-empty-state
                            title="No payments recorded"
                            description="Scheduled rent payments for this tenancy will appear here."
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="mt-4">
        <x-button variant="ghost" href="{{ route('tenancies.mine') }}">Back</x-button>
    </div>
</x-container>
@endsection
