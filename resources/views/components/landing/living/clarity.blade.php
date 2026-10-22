@props([
    'clarity' => [],
    'tenancy' => [],
])

<section class="mg-section ml-clarity" aria-labelledby="ml-clarity-heading">
    <div class="mg-container ml-clarity__grid">
        <div>
            <h2 class="mg-h2" id="ml-clarity-heading">{{ $clarity['title'] ?? 'Living with clarity' }}</h2>
            <p class="ml-clarity__lead">{{ $clarity['lead'] ?? '' }}</p>
            <div class="mh-feats ml-clarity__feats">
                @foreach(($clarity['features'] ?? []) as $feat)
                    <div>
                        <b>{{ $feat['title'] }}</b>
                        <span>{{ $feat['body'] }}</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ $clarity['cta_href'] ?? '#path' }}" class="mg-btn mg-btn--green">
                {{ $clarity['cta'] ?? 'See how it works' }}
            </a>
        </div>
        <x-landing.living.tenancy-preview :tenancy="$tenancy" />
    </div>
</section>
