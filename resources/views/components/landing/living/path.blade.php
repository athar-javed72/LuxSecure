@props([
    'steps' => [],
    'audiences' => [],
])

<section class="mg-section ml-path" id="path" aria-labelledby="ml-path-heading">
    <div class="mg-container">
        <h2 class="mg-h2" id="ml-path-heading">A clear path forward</h2>
        <div class="mh-steps ml-path__steps">
            @foreach($steps as $step)
                <div>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['body'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="ml-audience" id="landlords">
            @foreach($audiences as $audience)
                <article class="ml-audience__card">
                    <h3>{{ $audience['title'] }}</h3>
                    <ul>
                        @foreach(($audience['bullets'] ?? []) as $bullet)
                            <li>{{ $bullet }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('contact', ['type' => $audience['cta_type'] ?? 'living']) }}" class="ml-audience__link">
                        {{ $audience['cta'] ?? 'Learn more' }} →
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
