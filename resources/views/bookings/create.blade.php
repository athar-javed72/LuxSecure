@extends('include.master')

@section('title', 'Book '.$listing->title)

@section('content')
@php
    $roomTypeOptions = $roomTypes->mapWithKeys(fn ($type) => [
        $type->id => $type->name.' · up to '.$type->capacity.' · from '.number_format((float) $type->base_price, 0).'/night',
    ])->all();
@endphp

<x-container class="py-8">
    <x-breadcrumbs :items="[
        ['label' => $listing->title, 'href' => route('properties.show', $listing)],
        ['label' => 'Book'],
    ]" />

    <div class="max-w-xl"
         x-data="bookingQuote({
            quoteUrl: @js(route('bookings.quote')),
            roomTypeId: {{ (int) $selectedRoomTypeId }},
            csrf: @js(csrf_token())
         })">
        <x-page-header
            :title="'Book '.$listing->title"
            subtitle="Select dates to see live pricing and availability."
        />

        <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4">
            @csrf
            <x-panel>
                <x-select
                    label="Room type"
                    name="room_type_id"
                    id="room_type_id"
                    :options="$roomTypeOptions"
                    :selected="$selectedRoomTypeId"
                    x-model.number="roomTypeId"
                    @change="fetchQuote()"
                    required
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <x-input
                        label="Check-in"
                        name="check_in"
                        id="check_in"
                        type="date"
                        :value="old('check_in')"
                        x-model="checkIn"
                        @change="fetchQuote()"
                        required
                    />
                    <x-input
                        label="Check-out"
                        name="check_out"
                        id="check_out"
                        type="date"
                        :value="old('check_out')"
                        x-model="checkOut"
                        @change="fetchQuote()"
                        required
                    />
                </div>

                <div class="mt-4">
                    <x-input label="Guests" name="guests" type="number" min="1" :value="old('guests', 1)" required />
                </div>

                <div class="mt-4">
                    <x-select
                        label="Cancellation policy"
                        name="cancellation_policy"
                        :options="collect(App\Models\Booking::CANCELLATION_POLICIES)->mapWithKeys(fn ($p) => [$p => ucfirst($p)])->all()"
                        :selected="old('cancellation_policy', 'flexible')"
                    />
                </div>

                <div class="mt-4 rounded-sm bg-fs-surface p-4 text-sm" x-show="loaded" x-cloak>
                    <template x-if="available">
                        <p>
                            <span class="font-semibold text-fs-primary" x-text="nights + ' night(s)'"></span>
                            · Total <span class="font-semibold text-fs-brand" x-text="totalLabel"></span>
                            · <span x-text="availableRooms + ' room(s) left'"></span>
                        </p>
                    </template>
                    <template x-if="!available && checkIn && checkOut">
                        <p class="text-fs-danger">Those dates are not available.</p>
                    </template>
                </div>

                <div class="mt-4">
                    <x-button type="submit" variant="primary" class="w-full" x-bind:disabled="!available">
                        Confirm booking
                    </x-button>
                </div>
            </x-panel>
        </form>
    </div>
</x-container>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bookingQuote', (cfg) => ({
        quoteUrl: cfg.quoteUrl,
        roomTypeId: cfg.roomTypeId,
        checkIn: '',
        checkOut: '',
        nights: 0,
        total: 0,
        available: false,
        availableRooms: 0,
        loaded: false,
        get totalLabel() {
            return Number(this.total || 0).toLocaleString();
        },
        async fetchQuote() {
            if (!this.checkIn || !this.checkOut || this.checkOut <= this.checkIn) {
                this.loaded = false;
                this.available = false;
                return;
            }
            const params = new URLSearchParams({
                room_type_id: this.roomTypeId,
                check_in: this.checkIn,
                check_out: this.checkOut,
            });
            const res = await fetch(`${this.quoteUrl}?${params.toString()}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) {
                this.available = false;
                this.loaded = true;
                return;
            }
            const data = await res.json();
            this.nights = data.nights;
            this.total = data.total;
            this.available = data.available;
            this.availableRooms = data.available_rooms;
            this.loaded = true;
        },
    }));
});
</script>
@endpush
