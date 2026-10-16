@props([
    'quote',
    'name',
    'role' => 'Homebuyer',
    'avatar' => null,
])

<blockquote {{ $attributes->merge(['class' => 'bg-white rounded-md shadow-sm p-6 h-full flex flex-col']) }}>
    <p class="text-sm text-fs-secondary flex-1">“{{ $quote }}”</p>
    <footer class="mt-6 flex items-center gap-3">
        @if($avatar)
            <img src="{{ $avatar }}" alt="{{ $name }}" loading="lazy" width="44" height="44" class="w-11 h-11 rounded-full object-cover">
        @else
            <span class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-fs-brand-light text-fs-brand text-sm font-semibold">
                {{ mb_substr($name, 0, 1) }}
            </span>
        @endif
        <div>
            <cite class="not-italic text-sm font-semibold text-fs-primary">{{ $name }}</cite>
            <p class="text-xs text-fs-muted">{{ $role }}</p>
        </div>
    </footer>
</blockquote>
