@props(['faqs' => []])

<section class="mg-section mh-faq-section" aria-labelledby="mh-faq-heading">
    <div class="mg-container">
        <h2 class="mg-h2" id="mh-faq-heading">Questions about stays</h2>
        <div class="mh-faq" data-mh-faq>
            @foreach($faqs as $i => $item)
                <div class="mh-faq__item {{ $i === 0 ? 'is-open' : '' }}" data-mh-faq-item>
                    <button
                        type="button"
                        class="mh-faq__trigger"
                        id="mh-faq-btn-{{ $i }}"
                        aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                        aria-controls="mh-faq-panel-{{ $i }}"
                        data-mh-faq-trigger
                    >
                        <span>{{ $item['q'] }}</span>
                        <span class="mh-faq__icon" aria-hidden="true"></span>
                    </button>
                    <div
                        class="mh-faq__panel"
                        id="mh-faq-panel-{{ $i }}"
                        role="region"
                        aria-labelledby="mh-faq-btn-{{ $i }}"
                        data-mh-faq-panel
                    >
                        <div class="mh-faq__panel-inner">
                            <p>{{ $item['a'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
