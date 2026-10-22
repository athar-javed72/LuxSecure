@props(['card' => []])

<article class="ml-request-card">
    <h3>{{ $card['title'] ?? 'Tell us what you need' }}</h3>
    <p>{{ $card['body'] ?? '' }}</p>
    <a href="{{ route('contact', ['type' => 'living']) }}" class="mg-btn mg-btn--gold">
        {{ $card['cta'] ?? 'Request a rental' }}
    </a>
</article>
