@extends('include.master')

@section('title', 'Book '.$listing->title)

@section('content')
@php
    $roomTypeOptions = $roomTypes->mapWithKeys(fn ($type) => [
        $type->id => $type->name.' · up to '.$type->capacity.' · from '.number_format((float) $type->base_price, 0).'/night',
    ])->all();
    $bookingQuoteConfig = [
        'quoteUrl' => route('bookings.quote'),
        'roomTypeId' => (int) $selectedRoomTypeId,
    ];
@endphp

<x-container class="py-8">
    <x-breadcrumbs :items="[
        ['label' => $listing->title, 'href' => route('properties.show', $listing)],
        ['label' => 'Book'],
    ]" />

    <div
        class="max-w-xl"
        x-data="bookingQuote"
        data-alpine-config="{{ json_encode($bookingQuoteConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    >
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
                            <span class="font-semibold text-fs-primary" x-text="nightsLabel"></span>
                            · Total <span class="font-semibold text-fs-brand" x-text="totalLabel"></span>
                            · <span x-text="roomsLeftLabel"></span>
                        </p>
                    </template>
                    <template x-if="showUnavailable">
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
