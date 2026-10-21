@props(['items' => []])

<section class="mg-section mh-gallery" aria-labelledby="mh-gallery-heading">
    <div class="mg-container">
        <h2 class="mg-h2" id="mh-gallery-heading">Spaces in view</h2>
        <div class="mh-gal" data-mh-gallery>
            @foreach($items as $i => $item)
                <button
                    type="button"
                    class="mh-gal__item {{ !empty($item['large']) ? 'mh-gal__item--big' : '' }}"
                    data-mh-gallery-open
                    data-index="{{ $i }}"
                    aria-label="Open gallery image: {{ $item['alt'] ?? 'Stay space' }}"
                >
                    <img
                        src="{{ $item['image'] }}"
                        alt="{{ $item['alt'] ?? '' }}"
                        width="1200"
                        height="900"
                        loading="lazy"
                        decoding="async"
                    >
                </button>
            @endforeach
        </div>
    </div>

    <div
        class="mh-lightbox"
        data-mh-lightbox
        hidden
        role="dialog"
        aria-modal="true"
        aria-label="Stay gallery"
    >
        <div class="mh-lightbox__backdrop" data-mh-lightbox-close tabindex="-1"></div>
        <div class="mh-lightbox__panel">
            <button type="button" class="mh-lightbox__close" data-mh-lightbox-close aria-label="Close gallery">
                <span aria-hidden="true">×</span>
            </button>
            <button type="button" class="mh-lightbox__nav mh-lightbox__nav--prev" data-mh-lightbox-prev aria-label="Previous image">
                <span aria-hidden="true">‹</span>
            </button>
            <figure class="mh-lightbox__figure">
                <img src="" alt="" data-mh-lightbox-img width="1600" height="1200">
                <figcaption class="mh-lightbox__cap" data-mh-lightbox-cap></figcaption>
            </figure>
            <button type="button" class="mh-lightbox__nav mh-lightbox__nav--next" data-mh-lightbox-next aria-label="Next image">
                <span aria-hidden="true">›</span>
            </button>
        </div>
    </div>
</section>
