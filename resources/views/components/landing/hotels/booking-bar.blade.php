@php
    use App\Http\Requests\HotelsSearchRequest;

    $today = now(config('app.timezone'))->toDateString();
    $destination = old('destination', request('destination', ''));
    $checkIn = old('check_in', request('check_in', ''));
    $checkOut = old('check_out', request('check_out', ''));
    $guests = (int) old('guests', request('guests', 2));
    if ($guests < 1 || $guests > HotelsSearchRequest::MAX_GUESTS) {
        $guests = 2;
    }
    $checkOutMin = $checkIn
        ? now(config('app.timezone'))->parse($checkIn)->addDay()->toDateString()
        : $today;
@endphp

<div class="mg-container mh-booking-wrap" id="mh-booking">
    <form
        class="mh-booking"
        method="get"
        action="{{ route('divisions.hospitality') }}"
        data-mh-booking
        novalidate
    >
        <div class="mh-booking__field mh-booking__field--dest">
            <label for="mh-destination">Destination</label>
            <input
                id="mh-destination"
                name="destination"
                type="text"
                value="{{ $destination }}"
                placeholder="Where to?"
                autocomplete="address-level2"
                maxlength="120"
            >
        </div>
        <div class="mh-booking__field">
            <label for="mh-checkin">Check in</label>
            <input
                id="mh-checkin"
                name="check_in"
                type="date"
                value="{{ $checkIn }}"
                min="{{ $today }}"
                data-mh-checkin
            >
        </div>
        <div class="mh-booking__field">
            <label for="mh-checkout">Check out</label>
            <input
                id="mh-checkout"
                name="check_out"
                type="date"
                value="{{ $checkOut }}"
                min="{{ $checkOutMin }}"
                data-mh-checkout
            >
        </div>
        <div class="mh-booking__field">
            <label for="mh-guests">Guests</label>
            <select id="mh-guests" name="guests">
                @for($i = 1; $i <= HotelsSearchRequest::MAX_GUESTS; $i++)
                    <option value="{{ $i }}" @selected($guests === $i)>
                        {{ $i === HotelsSearchRequest::MAX_GUESTS ? $i.'+ guests' : ($i === 1 ? '1 guest' : $i.' guests') }}
                    </option>
                @endfor
            </select>
        </div>
        <button type="submit" class="mh-booking__submit" data-mh-submit>
            Check availability
        </button>
    </form>

    <div
        class="mh-booking__alerts"
        data-mh-booking-errors
        role="alert"
        aria-live="assertive"
        @if(! $errors->any()) hidden @endif
    >
        @foreach($errors->all() as $message)
            <p>{{ $message }}</p>
        @endforeach
    </div>
</div>
