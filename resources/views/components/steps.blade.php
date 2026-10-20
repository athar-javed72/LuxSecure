@props(['steps' => []])

@if(count($steps) > 0)
    <ol {{ $attributes->class(['grid gap-6 md:grid-cols-3']) }}>
        @foreach($steps as $i => $step)
            <li class="relative" data-reveal>
                <span class="fs-numeral font-[family-name:var(--font-display)] text-4xl text-[var(--color-gold-dark)] font-semibold" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="mt-3 text-lg font-semibold text-[var(--color-ink)]">{{ $step['title'] }}</h3>
                @if(!empty($step['body']))
                    <p class="mt-2 text-sm text-[var(--color-muted-strong)] max-w-[36ch]">{{ $step['body'] }}</p>
                @endif
            </li>
        @endforeach
    </ol>
@endif
