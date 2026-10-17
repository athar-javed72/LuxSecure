@props([
    'name',
    'role' => 'Real Estate Agent',
    'image',
    'phone' => null,
    'listings' => null,
    'href' => '#agents',
    'verified' => false,
    'rating' => null,
])

<article {{ $attributes->merge(['class' => 'bg-white rounded-md shadow-sm hover:shadow-lg transition-shadow duration-150 overflow-hidden h-full']) }}>
    <a href="{{ $href }}" class="block focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand">
        <div class="aspect-[4/3] bg-slate-100 overflow-hidden">
            <img
                src="{{ $image }}"
                alt="{{ $name }}"
                loading="lazy"
                width="480"
                height="360"
                class="w-full h-full object-cover"
            >
        </div>
        <div class="p-4">
            <h3 class="text-base font-semibold text-fs-primary flex items-center gap-2">
                {{ $name }}
                @if($verified)
                    <span class="text-[10px] uppercase tracking-wide text-fs-brand">Verified</span>
                @endif
            </h3>
            <p class="mt-1 text-sm text-fs-secondary">{{ $role }}</p>
            @if($rating !== null)
                <p class="mt-1 text-xs text-fs-muted">{{ number_format((float) $rating, 1) }}/5 avg</p>
            @endif
            @if($listings !== null)
                <p class="mt-2 text-xs text-fs-muted">{{ $listings }} listings</p>
            @endif
            @if($phone)
                <p class="mt-2 text-sm text-fs-brand font-medium">{{ $phone }}</p>
            @endif
        </div>
    </a>
</article>
