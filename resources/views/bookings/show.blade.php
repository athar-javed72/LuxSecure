@extends('include.master')

@section('title', 'Booking #'.$booking->id)

@section('content')
<x-container class="py-8 max-w-2xl">
    <x-page-header title="Booking #{{ $booking->id }}">
        <x-slot:actions>
            <x-status-badge :status="$booking->status" />
        </x-slot:actions>
    </x-page-header>

    <x-panel class="space-y-2 text-sm">
        <p><span class="text-fs-muted">Hotel:</span> <span class="text-fs-primary">{{ $booking->listing->title }}</span></p>
        <p><span class="text-fs-muted">Room type:</span> {{ $booking->roomType->name }}</p>
        @if($booking->room)<p><span class="text-fs-muted">Room:</span> {{ $booking->room->number }}</p>@endif
        <p><span class="text-fs-muted">Dates:</span> {{ $booking->check_in->toDateString() }} → {{ $booking->check_out->toDateString() }}</p>
        <p><span class="text-fs-muted">Guests:</span> {{ $booking->guests }}</p>
        <p><span class="text-fs-muted">Total:</span> {{ number_format((float) $booking->total, 2) }}</p>
        <p><span class="text-fs-muted">Cancellation:</span> {{ $booking->cancellation_policy }}</p>
    </x-panel>

    <x-panel class="mt-6">
        <h2 class="fs-display text-lg text-fs-primary">Pay for this booking</h2>
        <p class="mt-1 text-sm text-fs-secondary">Choose a gateway and upload proof when paying by bank transfer.</p>
        <form method="POST" action="{{ route('payments.initiate') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="payable_type" value="booking">
            <input type="hidden" name="payable_id" value="{{ $booking->id }}">
            @php
                $gatewayOptions = collect(\App\Models\Payment::enabledGateways())
                    ->mapWithKeys(fn ($g) => [$g => ucfirst(str_replace('_', ' ', $g))])
                    ->all();
            @endphp
            <x-select label="Gateway" name="gateway" :options="$gatewayOptions" :selected="old('gateway', 'bank_transfer')" required />
            <x-input label="Bank reference" name="bank_reference" :value="old('bank_reference')" />
            <x-file-upload
                label="Payment proof"
                name="proof"
                accept=".jpg,.jpeg,.png,.pdf"
                hint="Required for bank transfer. JPG, PNG, or PDF up to 5MB."
            />
            <x-button type="submit" variant="primary">Initiate payment</x-button>
        </form>
    </x-panel>

    <div class="mt-4">
        <x-button variant="ghost" href="{{ route('bookings.mine') }}">Back to my bookings</x-button>
    </div>
</x-container>
@endsection
