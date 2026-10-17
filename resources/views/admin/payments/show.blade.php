@extends('admin.layout')

@section('title', 'Payment')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Payments', 'href' => route('admin.payments.index')],
    ['label' => 'Detail'],
]" />

<x-page-header :title="'Payment '.$payment->uuid" />

<x-panel class="max-w-2xl space-y-4">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Amount</dt><dd class="mt-1 text-fs-primary">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Status</dt><dd class="mt-1"><x-status-badge :status="$payment->status" /></dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Gateway</dt><dd class="mt-1">{{ $payment->gateway }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">User</dt><dd class="mt-1">{{ $payment->user?->email }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">External ID</dt><dd class="mt-1">{{ $payment->external_id ?: '—' }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Paid at</dt><dd class="mt-1">{{ optional($payment->paid_at)->toDateTimeString() ?: '—' }}</dd></div>
    </dl>

    <div class="flex flex-wrap gap-2 pt-2 border-t border-black/5">
        @if($payment->gateway === 'bank_transfer' && $payment->status === 'awaiting_approval' && $payment->proof_path)
            <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                @csrf
                <x-button type="submit" variant="primary">Approve bank transfer</x-button>
            </form>
        @endif

        @if(in_array($payment->status, ['paid', 'refund_pending'], true) && auth()->user()->can('payments.refund'))
            <form method="POST" action="{{ route('admin.payments.refund', $payment) }}">
                @csrf
                <x-button type="submit" variant="ghost">Mark refunded</x-button>
            </form>
        @endif

        @if($payment->invoice)
            <x-button variant="ghost" href="{{ route('admin.payments.invoice', $payment) }}">Download invoice PDF</x-button>
        @endif
    </div>
</x-panel>
@endsection
