@extends('include.master')

@section('title', 'Tenancy details')

@section('content')
<x-container class="py-8 max-w-3xl">
    <h1 class="text-2xl font-bold text-fs-primary">{{ $tenancy->listing->title }}</h1>
    <p class="mt-2 text-sm text-fs-secondary">Landlord: {{ $tenancy->landlord->name }}</p>

    <div class="mt-6 rounded-md bg-white border border-black/5 overflow-hidden">
        <div class="px-4 py-3 border-b border-black/5 font-semibold">Payment history</div>
        <table class="w-full text-sm">
            <thead class="bg-fs-surface text-left text-xs uppercase text-fs-muted">
                <tr>
                    <th class="px-4 py-3">Due</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Paid</th>
                    <th class="px-4 py-3">Receipt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($tenancy->payments as $payment)
                    <tr>
                        <td class="px-4 py-3">{{ $payment->due_date->toDateString() }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $payment->amount, 0) }}</td>
                        <td class="px-4 py-3 capitalize">{{ $payment->status }}</td>
                        <td class="px-4 py-3">{{ optional($payment->paid_at)->toDateString() ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $payment->receipt_no ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <x-button variant="ghost" href="{{ route('tenancies.mine') }}">Back</x-button>
    </div>
</x-container>
@endsection
