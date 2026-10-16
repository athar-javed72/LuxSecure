@props([
    'title',
    'excerpt',
    'image',
    'date',
    'category' => 'Insights',
    'href' => '#blog',
])

<article {{ $attributes->merge(['class' => 'bg-white rounded-md shadow-sm hover:shadow-lg transition-shadow duration-150 overflow-hidden h-full flex flex-col']) }}>
    <a href="{{ $href }}" class="flex flex-col h-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand">
        <div class="aspect-[4/3] bg-slate-100 overflow-hidden">
            <img
                src="{{ $image }}"
                alt="{{ $title }}"
                loading="lazy"
                width="640"
                height="480"
                class="w-full h-full object-cover"
            >
        </div>
        <div class="p-4 flex flex-col flex-1">
            <div class="flex items-center gap-2 mb-2">
                <x-badge tone="muted">{{ $category }}</x-badge>
                <time class="text-xs text-fs-muted" datetime="{{ $date }}">{{ \Illuminate\Support\Carbon::parse($date)->format('M j, Y') }}</time>
            </div>
            <h3 class="text-base font-semibold text-fs-primary fs-line-clamp-2">{{ $title }}</h3>
            <p class="mt-2 text-sm text-fs-secondary fs-line-clamp-2 flex-1">{{ $excerpt }}</p>
        </div>
    </a>
</article>
