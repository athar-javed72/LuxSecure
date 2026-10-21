@props(['why' => []])

<section class="mg-section mh-why" aria-labelledby="mh-why-heading">
    <div class="mg-container mh-why__grid">
        <div class="mh-why__media">
            <img
                src="{{ $why['image'] ?? '' }}"
                alt="{{ $why['image_alt'] ?? '' }}"
                width="880"
                height="1100"
                loading="lazy"
                decoding="async"
            >
        </div>
        <div>
            <h2 class="mg-h2 mh-why__title" id="mh-why-heading">Hospitality with a quieter pace</h2>
            <p class="mh-why__lead">Spaces chosen for character and comfort, explored with a team that follows through.</p>
            <div class="mh-feats">
                @foreach(($why['features'] ?? []) as $feat)
                    <div>
                        <b>{{ $feat['title'] }}</b>
                        <span>{{ $feat['body'] }}</span>
                    </div>
                @endforeach
            </div>
            <x-landing.hotels.refund-tiles :tiles="$why['refunds'] ?? []" />
        </div>
    </div>
</section>
