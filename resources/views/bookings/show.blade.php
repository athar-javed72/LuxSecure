@extends('include.master')

@section('title', 'Booking #'.$booking->id)

@section('content')
@php
    use App\Services\Payments\RefundCalculator;
    $paidAmount = (float) $booking->payments->where('status', 'paid')->sum('amount');
    $pendingPayment = $booking->payments->firstWhere('status', 'pending')
        ?? $booking->payments->firstWhere('status', 'awaiting_approval');
    $canCancel = in_array($booking->status, ['pending', 'confirmed'], true);
    $refundPreview = $canCancel
        ? app(RefundCalculator::class)->forBooking($booking, $paidAmount > 0 ? $paidAmount : (float) $booking->total)
        : null;
@endphp

<x-container class="py-8 max-w-2xl">
    <x-breadcrumbs :items="[
        ['label' => 'My bookings', 'href' => route('bookings.mine')],
        ['label' => 'Booking #'.$booking->id],
    ]" />

    <x-page-header title="Booking #{{ $booking->id }}" :subtitle="$booking->listing->title">
        <x-slot:actions>
            <x-status-badge :status="$booking->status" />
        </x-slot:actions>
    </x-page-header>

    @if($booking->status === 'pending' && $booking->hold_expires_at)
        <x-alert tone="info" class="mb-6" dismissible>
            Room hold expires
            <strong class="font-semibold">{{ $booking->hold_expires_at->timezone(config('app.timezone'))->format('M j, Y g:i A T') }}</strong>.
            Complete payment before then or the reservation may be released.
        </x-alert>
    @endif

    <x-panel class="space-y-3 text-sm">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Room type</dt>
                <dd class="mt-1 text-fs-primary">{{ $booking->roomType->name }}</dd>
            </div>
            @if($booking->room)
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Room</dt>
                    <dd class="mt-1 text-fs-primary">{{ $booking->room->number }}</dd>
                </div>
            @endif
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Dates</dt>
                <dd class="mt-1 text-fs-primary">{{ $booking->check_in->toDateString() }} → {{ $booking->check_out->toDateString() }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Guests</dt>
                <dd class="mt-1 text-fs-primary">{{ $booking->guests }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Total</dt>
                <dd class="mt-1 font-semibold text-fs-brand">{{ \App\Support\Money::format($booking->total, $booking->currency) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-fs-muted">Cancellation</dt>
                <dd class="mt-1 text-fs-primary capitalize">{{ $booking->cancellation_policy }}</dd>
            </div>
        </dl>
    </x-panel>

    @if(in_array($booking->status, ['pending', 'confirmed'], true) && ! $booking->payments->contains('status', 'paid'))
        <x-panel class="mt-6">
            <h2 class="fs-display text-lg text-fs-primary">Pay for this booking</h2>
            <p class="mt-1 text-sm text-fs-secondary">Choose a gateway. Bank transfer requires a reference and proof upload.</p>
            <form method="POST" action="{{ route('payments.initiate') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="payable_type" value="booking">
                <input type="hidden" name="payable_id" value="{{ $booking->id }}">
                @php
                    $gatewayOptions = collect(\App\Models\Payment::enabledGateways())
                        ->mapWithKeys(fn ($g) => [$g => ucfirst(str_replace('_', ' ', $g))])
                        ->all();
                @endphp
                <x-select label="Gateway" name="gateway" id="pay_gateway" :options="$gatewayOptions" :selected="old('gateway', 'bank_transfer')" required />
                <x-input label="Bank reference" name="bank_reference" :value="old('bank_reference')" hint="Required for bank transfer." />
                <x-file-upload
                    label="Payment proof"
                    name="proof"
                    accept=".jpg,.jpeg,.png,.pdf"
                    hint="Required for bank transfer. JPG, PNG, or PDF up to 5MB."
                />
                <x-button type="submit" variant="primary">Initiate payment</x-button>
            </form>
        </x-panel>
    @endif

    @if($booking->payments->isNotEmpty())
        <x-panel class="mt-6">
            <h2 class="fs-display text-lg text-fs-primary mb-3">Payment history</h2>
            <ul class="space-y-3 text-sm">
                @foreach($booking->payments as $payment)
                    <li class="flex flex-wrap items-center justify-between gap-2 border-b border-black/5 pb-3 last:border-0 last:pb-0">
                        <div>
                            <p class="font-medium text-fs-primary capitalize">{{ str_replace('_', ' ', $payment->gateway) }}</p>
                            <p class="text-xs text-fs-muted">{{ $payment->created_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-fs-primary">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</p>
                            <x-status-badge :status="$payment->status" />
                        </div>
                    </li>
                @endforeach
            </ul>
            @if($pendingPayment)
                <p class="mt-3 text-xs text-fs-secondary">A pending payment is awaiting admin review{{ $pendingPayment->proof_path ? ' (proof uploaded)' : '' }}.</p>
            @endif
        </x-panel>
    @endif

    @if($canCancel && $refundPreview)
        <x-panel class="mt-6 border-fs-danger/20">
            <h2 class="fs-display text-lg text-fs-primary">Cancel booking</h2>
            <p class="mt-2 text-sm text-fs-secondary">
                Refund preview ({{ $refundPreview['reason'] }}):
                <span class="font-semibold text-fs-primary">{{ $refundPreview['refund_percent'] }}%</span>
                @if((float) $refundPreview['refund_amount'] > 0)
                    · <span class="font-semibold text-fs-brand">{{ \App\Support\Money::format($refundPreview['refund_amount'], $booking->currency) }}</span>
                @else
                    · no refund
                @endif
            </p>
            <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-4">
                @csrf
                <x-button type="submit" variant="ghost" class="!text-fs-danger">Confirm cancellation</x-button>
            </form>
        </x-panel>
    @endif

    <div class="mt-6">
        <x-button variant="ghost" href="{{ route('bookings.mine') }}">Back to my bookings</x-button>
    </div>
</x-container>
@endsection
