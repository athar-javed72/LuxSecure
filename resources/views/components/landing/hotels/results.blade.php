@props([
    'search' => null,
])

@if(! empty($search['active']))
@php
    $stays = $search['stays'] ?? collect();
    $count = (int) ($search['count'] ?? $stays->count());
    $summary = $search['summary']['label'] ?? '';
    $error = $search['error'] ?? null;
    $enquireHref = route('contact', ['type' => 'hospitality']);
@endphp

<section class="mg-section mh-results" id="mh-results" aria-labelledby="mh-results-heading" data-mh-results>
    <div class="mg-container">
        <div class="mh-results__head">
            <div>
                <h2 class="mg-h2" id="mh-results-heading">Available stays</h2>
                @if($summary)
                    <p class="mh-results__summary">
                        <a href="#mh-booking">{{ $summary }}</a>
                    </p>
                @endif
            </div>
            @unless($error)
                <p class="mh-results__count" aria-live="polite">
                    {{ $count }} {{ $count === 1 ? 'stay' : 'stays' }} found
                </p>
            @endunless
        </div>

        @if($error)
            <div class="mh-results__state mh-results__state--error" role="alert">
                <p>{{ $error }}</p>
                <a href="#mh-booking" class="mg-btn mg-btn--gold">Try again</a>
            </div>
        @elseif($count === 0)
            <div class="mh-results__state" role="status">
                <p>No stays for these dates</p>
                <div class="mh-results__actions">
                    <a href="#mh-booking" class="mg-btn mg-btn--gold">Change dates</a>
                    <a href="{{ $enquireHref }}" class="mg-btn mg-btn--ghost">Enquire for groups</a>
                </div>
            </div>
        @else
            <div class="mh-stays-grid">
                @foreach($stays as $stay)
                    <x-landing.hotels.stay-card :stay="$stay" />
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif
