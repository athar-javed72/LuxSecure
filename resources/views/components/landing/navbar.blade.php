@php
    $links = [
        ['label' => 'Properties', 'href' => route('properties'), 'key' => 'Properties'],
        ['label' => 'Hotels', 'href' => route('divisions.hospitality'), 'key' => 'Hotels'],
        ['label' => 'Living', 'href' => route('divisions.living'), 'key' => 'Living'],
        ['label' => 'About', 'href' => url('/#about'), 'key' => 'About'],
    ];

    $active = match (true) {
        request()->routeIs('divisions.hospitality') => 'Hotels',
        request()->routeIs('divisions.living') => 'Living',
        request()->routeIs('properties*') => 'Properties',
        default => null,
    };

    $isDivisionLanding = request()->routeIs('divisions.hospitality', 'divisions.living');
    $ctaLabel = $isDivisionLanding ? 'Enquire' : 'List your property';
    $ctaHref = match (true) {
        request()->routeIs('divisions.hospitality') => route('contact', ['type' => 'hospitality']),
        request()->routeIs('divisions.living') => route('contact', ['type' => 'living']),
        default => route('contact', ['type' => 'property']),
    };
@endphp

<header class="mg-nav">
    <div class="mg-container mg-nav__inner">
        <a href="{{ route('home') }}" class="mg-logo" aria-label="Maskanoor home">
            <img class="mg-logo__mark" src="{{ \App\Support\BrandAssets::logo('monogram-nostar', 'dark') }}" alt="" width="28" height="28" decoding="async">
            <span>Maskanoor</span>
        </a>

        <nav class="mg-nav__links" aria-label="Main">
            @foreach($links as $link)
                <a
                    href="{{ $link['href'] }}"
                    @class(['is-active' => $active === $link['key']])
                    @if($active === $link['key']) aria-current="page" @endif
                >{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="mg-nav__actions">
            @if($isDivisionLanding)
                <span class="mg-nav__pill mg-nav__pill--hide-sm" aria-label="Language">EN | اردو</span>
                <span class="mg-nav__pill mg-nav__pill--hide-sm" aria-label="Currency">PKR</span>
            @else
                <span class="mg-nav__meta" aria-label="Language and currency">EN · PKR</span>
            @endif
            <a href="{{ route('login.form') }}" class="mg-nav__login">Login</a>
            <a href="{{ $ctaHref }}" class="mg-btn mg-btn--gold">{{ $ctaLabel }}</a>
        </div>

        <button
            type="button"
            class="mg-nav__toggle"
            data-mg-nav-toggle
            aria-expanded="false"
            aria-controls="mg-nav-drawer"
            aria-label="Open menu"
        >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </div>

    <div id="mg-nav-drawer" class="mg-nav__drawer" data-mg-nav-drawer>
        @foreach($links as $link)
            <a
                href="{{ $link['href'] }}"
                @class(['is-active' => $active === $link['key']])
            >{{ $link['label'] }}</a>
        @endforeach
        <span class="mg-nav__drawer-meta">{{ $isDivisionLanding ? 'EN | اردو · PKR' : 'EN · PKR' }}</span>
        <a href="{{ route('login.form') }}">Login</a>
        <a href="{{ $ctaHref }}">{{ $ctaLabel }}</a>
    </div>
</header>
