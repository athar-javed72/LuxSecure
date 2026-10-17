@props([
    'title',
    'description' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-md bg-fs-primary text-white px-6 py-12 text-center border border-white/5 shadow-lg']) }}>
    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-fs-brand">LuxSecure</p>
    <h3 class="fs-display mt-3 text-xl md:text-2xl font-semibold text-white">{{ $title }}</h3>
    @if($description)
        <p class="mt-2 text-sm text-slate-300 max-w-md mx-auto">{{ $description }}</p>
    @endif
    @if($actionLabel && $actionHref)
        <div class="mt-6 flex justify-center">
            <x-button variant="primary" :href="$actionHref">{{ $actionLabel }}</x-button>
        </div>
    @elseif(isset($action))
        <div class="mt-6 flex justify-center">{{ $action }}</div>
    @endif
</div>
