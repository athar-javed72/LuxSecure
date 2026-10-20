@props(['destinations' => []])

@php $destinations = collect($destinations); @endphp

@if($destinations->isNotEmpty())
    <div {{ $attributes->class([
        'grid gap-4',
        $destinations->count() <= 2 ? 'md:grid-cols-1' : 'sm:grid-cols-2 lg:grid-cols-3',
    ]) }}>
        @foreach($destinations as $dest)
            <a href="{{ $dest['href'] }}" class="group relative overflow-hidden rounded-[var(--radius-card)] {{ $destinations->count() <= 2 ? 'min-h-[220px] md:min-h-[280px]' : 'aspect-[4/3]' }} focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]">
                @if(!empty($dest['cover']))
                    <img src="{{ $dest['cover'] }}" alt="" width="1200" height="900" loading="lazy" decoding="async" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]" style="object-position: {{ $dest['focal'] ?? '50% 50%' }}">
                @else
                    <div class="absolute inset-0 bg-[var(--color-forest)]">
                        <img src="{{ \App\Support\BrandAssets::logo('monogram-nostar', 'dark') }}" alt="" class="absolute inset-0 m-auto w-16 opacity-[0.08]" width="64" height="64">
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-[rgba(13,29,25,.75)] via-[rgba(13,29,25,.25)] to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-5 text-[var(--color-ivory)]">
                    <h3 class="font-[family-name:var(--font-display)] text-2xl font-semibold">{{ $dest['city']->name }}</h3>
                    <p class="mt-1 text-sm text-[var(--color-sage)] fs-numeral">{{ $dest['label'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
@endif
