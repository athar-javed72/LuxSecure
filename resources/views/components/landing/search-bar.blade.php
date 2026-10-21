<div class="mg-search-wrap">
    <div class="mg-container">
        <form class="mg-search" action="{{ route('properties') }}" method="GET" data-mg-search>
            <input type="hidden" name="purpose" value="sale">
            <div class="mg-search__tabs" role="tablist" aria-label="Purpose">
                <button type="button" class="mg-search__tab is-active" role="tab" aria-selected="true" data-mg-tab="sale">Buy</button>
                <button type="button" class="mg-search__tab" role="tab" aria-selected="false" data-mg-tab="rent">Rent</button>
                <button type="button" class="mg-search__tab" role="tab" aria-selected="false" data-mg-tab="short_stay">Stay</button>
            </div>
            <div class="mg-search__fields">
                <label class="mg-search__field" for="mg-location">
                    <span>Location</span>
                    <input id="mg-location" type="text" name="search" placeholder="City or area" autocomplete="off">
                </label>
                <button type="button" class="mg-search__field" aria-haspopup="listbox">
                    <span>Property type</span>
                    <strong>Any type</strong>
                </button>
                <button type="button" class="mg-search__field" aria-haspopup="listbox">
                    <span>Budget</span>
                    <strong>Any budget</strong>
                </button>
                <div class="mg-search__submit">
                    <button type="submit" class="mg-btn mg-btn--green">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>
