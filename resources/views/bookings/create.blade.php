@extends('include.master')

@section('title', 'Book '.$listing->title)

@section('content')
<x-container class="py-8">
    <nav class="mb-6 text-sm text-fs-secondary" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ route('properties.show', $listing) }}" class="hover:text-fs-brand">{{ $listing->title }}</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-fs-primary font-medium">Book</li>
        </ol>
    </nav>

    <div class="max-w-xl"
         x-data="bookingQuote({
            quoteUrl: @js(route('bookings.quote')),
            roomTypeId: {{ (int) $selectedRoomTypeId }},
            csrf: @js(csrf_token())
         })">
        <h1 class="text-2xl font-bold text-fs-primary">Book {{ $listing->title }}</h1>
        <p class="mt-2 text-sm text-fs-secondary">Select dates to see live pricing and availability.</p>

        <form method="POST" action="{{ route('bookings.store') }}" class="mt-6 space-y-4 rounded-md bg-white border border-black/5 p-6">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-fs-primary mb-1" for="room_type_id">Room type</label>
                <select id="room_type_id" name="room_type_id" x-model.number="roomTypeId" @change="fetchQuote()"
                        class="w-full px-4 py-3 rounded-sm border border-slate-200 bg-white text-sm">
                    @foreach ($roomTypes as $type)
                        <option value="{{ $type->id }}" @selected((int) $selectedRoomTypeId === (int) $type->id)>
                            {{ $type->name }} · up to {{ $type->capacity }} · from {{ number_format((float) $type->base_price, 0) }}/night
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-fs-primary mb-1" for="check_in">Check-in</label>
                    <input id="check_in" name="check_in" type="date" x-model="checkIn" @change="fetchQuote()"
                           value="{{ old('check_in') }}" required
                           class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm">
                    @error('check_in')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-fs-primary mb-1" for="check_out">Check-out</label>
                    <input id="check_out" name="check_out" type="date" x-model="checkOut" @change="fetchQuote()"
                           value="{{ old('check_out') }}" required
                           class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm">
                    @error('check_out')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
                </div>
            </div>

            <x-input label="Guests" name="guests" type="number" min="1" :value="old('guests', 1)" required />

            <x-select
                label="Cancellation policy"
                name="cancellation_policy"
                :options="collect(App\Models\Booking::CANCELLATION_POLICIES)->mapWithKeys(fn ($p) => [$p => ucfirst($p)])->all()"
                :selected="old('cancellation_policy', 'flexible')"
            />

            <div class="rounded-sm bg-fs-surface p-4 text-sm" x-show="loaded" x-cloak>
                <template x-if="available">
                    <p>
                        <span class="font-semibold" x-text="nights + ' night(s)'"></span>
                        · Total <span class="font-semibold text-fs-brand" x-text="totalLabel"></span>
                        · <span x-text="availableRooms + ' room(s) left'"></span>
                    </p>
                </template>
                <template x-if="!available && checkIn && checkOut">
                    <p class="text-fs-danger">Those dates are not available.</p>
                </template>
            </div>

            <button type="submit"
                    class="inline-flex items-center justify-center min-h-11 px-5 rounded-sm bg-fs-brand text-white text-sm font-semibold w-full disabled:opacity-50"
                    :disabled="!available">
                Confirm booking
            </button>
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
