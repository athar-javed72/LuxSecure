@php
    $countries = ['United Kingdom', 'Pakistan', 'Saudi Arabia', 'United Arab Emirates', 'Qatar', 'Malaysia'];
@endphp

<section class="mg-marquee" aria-label="Countries we serve">
    <div class="mg-marquee__track">
        @foreach([1, 2] as $dup)
            @foreach($countries as $i => $name)
                <span>{{ $name }}</span>
                <span class="mg-marquee__sep" aria-hidden="true">◆</span>
            @endforeach
        @endforeach
    </div>
</section>
