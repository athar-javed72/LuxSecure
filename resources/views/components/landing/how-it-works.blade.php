@php
    $steps = [
        [
            'title' => 'Search or enquire',
            'body' => 'Filter by country, city, price and type, or send one enquiry and our team will respond.',
        ],
        [
            'title' => 'Verify and book',
            'body' => 'Listings come from KYC-verified hosts and agents. Pay by bank transfer with a clear refund policy.',
        ],
        [
            'title' => 'Stay or move in',
            'body' => 'Track bookings, rent schedules and payments from one account.',
        ],
    ];

    $trust = [
        [
            'label' => 'KYC-verified',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        [
            'label' => 'Bank transfer',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>',
        ],
        [
            'label' => '6 Countries',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 010 18M12 3a15 15 0 000 18"/>',
        ],
        [
            'label' => '24/7 Support',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z"/>',
        ],
    ];
@endphp

<section class="mg-section mg-how" aria-labelledby="mg-how-heading">
    <div class="mg-container">
        <p class="mg-eyebrow">How it works</p>
        <h2 class="mg-h2" id="mg-how-heading">From first look to the keys</h2>

        <div class="mg-steps">
            @foreach($steps as $i => $step)
                <article class="mg-step">
                    <p class="mg-step__num" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</p>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['body'] }}</p>
                </article>
            @endforeach
        </div>

        <div class="mg-trust" role="list">
            @foreach($trust as $item)
                <div class="mg-trust__item" role="listitem">
                    <span class="mg-trust__icon" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">{!! $item['svg'] !!}</svg>
                    </span>
                    <span class="mg-trust__label">{{ $item['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
