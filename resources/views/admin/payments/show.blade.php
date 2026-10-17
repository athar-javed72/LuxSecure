@extends('admin.layout')

@section('title', 'Payment')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Payments', 'href' => route('admin.payments.index')],
    ['label' => 'Detail'],
]" />

<div class="max-w-2xl space-y-4 rounded-md bg-white border border-black/5 p-6">
    <h1 class="fs-display text-2xl text-fs-primary">Payment {{ $payment->uuid }}</h1>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div><dt class="text-xs text-fs-muted">Amount</dt><dd>{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Status</dt><dd>{{ $payment->status }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Gateway</dt><dd>{{ $payment->gateway }}</dd></div>
        <div><dt class="text-xs text-fs-muted">User</dt><dd>{{ $payment->user?->email }}</dd></div>
        <div><dt class="text-xs text-fs-muted">External ID</dt><dd>{{ $payment->external_id ?: '—' }}</dd></div>
        <div><dt class="text-xs text-fs-muted">Paid at</dt><dd>{{ optional($payment->paid_at)->toDateTimeString() ?: '—' }}</dd></div>
    </dl>

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
@endsection
