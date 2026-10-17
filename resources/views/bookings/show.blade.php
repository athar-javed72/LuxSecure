@extends('include.master')

@section('title', 'Booking #'.$booking->id)

@section('content')
<x-container class="py-8 max-w-2xl">
    <h1 class="text-2xl font-bold text-fs-primary">Booking #{{ $booking->id }}</h1>
    <div class="mt-6 rounded-md bg-white border border-black/5 p-6 space-y-2 text-sm">
        <p><span class="text-fs-muted">Hotel:</span> {{ $booking->listing->title }}</p>
        <p><span class="text-fs-muted">Room type:</span> {{ $booking->roomType->name }}</p>
        @if($booking->room)<p><span class="text-fs-muted">Room:</span> {{ $booking->room->number }}</p>@endif
        <p><span class="text-fs-muted">Dates:</span> {{ $booking->check_in->toDateString() }} → {{ $booking->check_out->toDateString() }}</p>
        <p><span class="text-fs-muted">Guests:</span> {{ $booking->guests }}</p>
        <p><span class="text-fs-muted">Total:</span> {{ number_format((float) $booking->total, 2) }}</p>
        <p><span class="text-fs-muted">Status:</span> <span class="capitalize">{{ $booking->status }}</span></p>
        <p><span class="text-fs-muted">Cancellation:</span> {{ $booking->cancellation_policy }}</p>
    </div>
    <div class="mt-6 rounded-md bg-white border border-black/5 p-6">
        <h2 class="font-semibold text-fs-primary">Pay for this booking</h2>
        <form method="POST" action="{{ route('payments.initiate') }}" enctype="multipart/form-data" class="mt-4 space-y-3">
            @csrf
            <input type="hidden" name="payable_type" value="booking">
            <input type="hidden" name="payable_id" value="{{ $booking->id }}">
            <x-select label="Gateway" name="gateway" :options="['bank_transfer' => 'Bank transfer', 'jazzcash' => 'JazzCash', 'easypaisa' => 'Easypaisa']" :selected="old('gateway', 'bank_transfer')" required />
            <x-input label="Bank reference" name="bank_reference" :value="old('bank_reference')" />
            <div>
                <label class="block text-xs font-semibold mb-1">Proof (bank transfer)</label>
                <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm">
            </div>
            <x-button type="submit" variant="primary">Initiate payment</x-button>
        </form>
    </div>

    <div class="mt-4">
        <x-button variant="ghost" href="{{ route('bookings.mine') }}">Back to my bookings</x-button>
    </div>
</x-container>
@endsection
