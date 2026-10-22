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
    $sort = $search['sort'] ?? null;
    $page = (int) ($search['page'] ?? 1);
    $hasMore = (bool) ($search['has_more'] ?? false);
    $queryBase = array_filter([
        'destination' => $search['destination'] ?? null,
        'check_in' => $search['check_in'] ?? null,
        'check_out' => $search['check_out'] ?? null,
        'guests' => $search['guests'] ?? null,
        'sort' => $sort,
    ], fn ($v) => $v !== null && $v !== '');
@endphp

<section class="mg-section mh-results" id="results" aria-labelledby="mh-results-heading" data-mh-results>
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
            <div class="mh-results__meta">
                @unless($error)
                    <p class="mh-results__count" aria-live="polite">
                        {{ $count }} {{ $count === 1 ? 'stay' : 'stays' }} found
                    </p>
                @endunless
                @if($count > 1 && ! $error)
                    <a
                        class="mh-results__sort"
                        href="{{ route('divisions.hospitality', array_merge($queryBase, ['sort' => 'price_asc', 'page' => 1])) }}#results"
                        @if($sort === 'price_asc') aria-current="true" @endif
                    >Price: low to high</a>
                @endif
            </div>
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
            @if($hasMore)
                <div class="mh-results__more">
                    <a
                        class="mg-btn mg-btn--ghost"
                        href="{{ route('divisions.hospitality', array_merge($queryBase, ['page' => $page + 1])) }}#results"
                    >Show more</a>
                </div>
            @endif
        @endif
    </div>
</section>
@endif
