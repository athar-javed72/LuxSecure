@props([
    'tabs' => [],
    'active' => null,
])

@php
    $activeKey = $active ?? (array_key_first($tabs) ?? null);
    $tabsConfig = ['tab' => $activeKey];
@endphp

<div
    x-data="tabs"
    data-alpine-config="{{ json_encode($tabsConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    {{ $attributes->merge(['class' => 'w-full']) }}
>
    <div class="flex flex-wrap gap-1 border-b border-black/10 mb-6" role="tablist">
        @foreach($tabs as $key => $label)
            <button
                type="button"
                role="tab"
                :aria-selected="ariaSelected(@js($key))"
                class="min-h-11 px-4 text-sm font-semibold border-b-2 -mb-px transition-colors duration-150"
                :class="tabClass(@js($key))"
                @click="setTab(@js($key))"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
    {{ $slot }}
</div>
