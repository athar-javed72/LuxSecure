@php
    $action = route('properties');
@endphp

<div class="mg-container mh-booking-wrap">
    <form class="mh-booking ml-search" method="get" action="{{ $action }}" data-ml-search>
        <input type="hidden" name="purpose" value="rent">
        <div class="mh-booking__field mh-booking__field--dest">
            <label for="ml-location">Location</label>
            <input id="ml-location" name="search" type="text" placeholder="City or area" autocomplete="address-level2">
        </div>
        <div class="mh-booking__field">
            <label for="ml-type">Property type</label>
            <select id="ml-type" name="type">
                <option value="">Any type</option>
                <option value="apartment">Apartment</option>
                <option value="house">House</option>
                <option value="villa">Villa</option>
            </select>
        </div>
        <div class="mh-booking__field">
            <label for="ml-beds">Bedrooms</label>
            <select id="ml-beds" name="bedrooms">
                <option value="">Any</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4+</option>
            </select>
        </div>
        <div class="mh-booking__field">
            <label for="ml-budget">Monthly budget</label>
            <select id="ml-budget" name="max_price">
                <option value="">Any budget</option>
                <option value="50000">Up to PKR 50,000</option>
                <option value="100000">Up to PKR 100,000</option>
                <option value="180000">Up to PKR 180,000</option>
                <option value="300000">Up to PKR 300,000</option>
            </select>
        </div>
        <button type="submit" class="mh-booking__submit">Search rentals</button>
    </form>
</div>
