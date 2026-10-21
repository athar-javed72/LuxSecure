@props(['stays' => []])

<section class="mg-section mh-stays" id="stays" aria-labelledby="mh-stays-heading">
    <div class="mg-container">
        <div class="mh-section-head">
            <h2 class="mg-h2" id="mh-stays-heading">Featured stays</h2>
            <a href="{{ route('divisions.hospitality', ['guests' => 2]) }}" class="mh-section-head__link">View all</a>
        </div>
        <div class="mh-stays-grid">
            @foreach($stays as $stay)
                <x-landing.hotels.stay-card :stay="$stay" />
            @endforeach
        </div>
    </div>
</section>
