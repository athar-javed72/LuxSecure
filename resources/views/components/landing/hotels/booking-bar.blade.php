@php
    $action = route('properties');
@endphp

<div class="mg-container mh-booking-wrap">
    <form class="mh-booking" method="get" action="{{ $action }}" data-mh-booking>
        <input type="hidden" name="purpose" value="short_stay">
        <div class="mh-booking__field mh-booking__field--dest">
            <label for="mh-destination">Destination</label>
            <input id="mh-destination" name="search" type="text" placeholder="Where to?" autocomplete="address-level2">
        </div>
        <div class="mh-booking__field">
            <label for="mh-checkin">Check in</label>
            <input id="mh-checkin" name="check_in" type="date" data-mh-checkin>
        </div>
        <div class="mh-booking__field">
            <label for="mh-checkout">Check out</label>
            <input id="mh-checkout" name="check_out" type="date" data-mh-checkout>
        </div>
        <div class="mh-booking__field">
            <label for="mh-guests">Guests</label>
            <select id="mh-guests" name="guests">
                <option value="1">1 guest</option>
                <option value="2" selected>2 guests</option>
                <option value="3">3 guests</option>
                <option value="4">4+ guests</option>
            </select>
        </div>
        <button type="submit" class="mh-booking__submit">Check availability</button>
    </form>
</div>
