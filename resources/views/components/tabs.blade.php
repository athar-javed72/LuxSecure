@props([
    'tabs' => [],
    'active' => null,
])

@php
    $activeKey = $active ?? (array_key_first($tabs) ?? null);
@endphp

<div
    x-data="{ tab: @js($activeKey) }"
    {{ $attributes->merge(['class' => 'w-full']) }}
>
    <div class="flex flex-wrap gap-1 border-b border-black/10 mb-6" role="tablist">
        @foreach($tabs as $key => $label)
            <button
                type="button"
                role="tab"
                :aria-selected="(tab === '{{ $key }}').toString()"
                class="min-h-11 px-4 text-sm font-semibold border-b-2 -mb-px transition-colors duration-150"
                :class="tab === '{{ $key }}' ? 'border-fs-brand text-fs-primary' : 'border-transparent text-fs-muted hover:text-fs-primary'"
                @click="tab = '{{ $key }}'"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
    {{ $slot }}
</div>
