@props([
    'suggestions' => [],
    'types' => [],
    'currency' => 'PKR',
])

@php
    $purposes = config('search.purposes', []);
    $presets = config('search.budget_presets', []);
    $icons = config('search.type_icons', []);
    $currentPurpose = request('purpose', '');
    $currentType = request('type', '');
    $currentPrice = request('price', '');
    $currentSearch = request('search', '');
    $typeLabel = 'Any type';
    if ($currentType) {
        $match = collect($types)->firstWhere('slug', $currentType);
        $typeLabel = $match['name'] ?? $currentType;
    }
    $budgetLabel = 'Any budget';
    foreach ($presets as $p) {
        if (($p['price'] ?? null) === $currentPrice) {
            $budgetLabel = $p['label'];
            break;
        }
    }
@endphp

<div
    {{ $attributes->class(['relative z-30 w-full']) }}
    data-property-search
    data-suggestions='@json(array_values($suggestions))'
>
    {{-- Mobile trigger --}}
    <button
        type="button"
        data-sheet-open
        class="lg:hidden flex items-center justify-between w-full h-14 px-4 fs-search-card text-left"
        aria-expanded="false"
        aria-controls="property-search-sheet"
    >
        <span class="text-sm text-[var(--color-muted)]">Search properties, hotels and stays</span>
        <svg class="w-5 h-5 text-[var(--color-forest)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
    </button>

    {{-- Desktop card --}}
    <form action="{{ route('properties') }}" method="GET" class="hidden lg:block fs-search-card overflow-visible">
        <input type="hidden" name="purpose" value="{{ $currentPurpose }}">
        <div class="fs-search-tabs flex gap-1 px-4 pt-3 border-b border-[var(--color-border)]" role="tablist" aria-label="Purpose">
            @foreach($purposes as $value => $meta)
                <button
                    type="button"
                    role="tab"
                    data-purpose-tab="{{ $value }}"
                    aria-selected="{{ $currentPurpose === $value ? 'true' : 'false' }}"
                    class="px-3 h-10 text-sm font-semibold text-[var(--color-muted)] {{ $currentPurpose === $value ? 'is-active' : '' }}"
                >{{ $meta['label'] }}</button>
            @endforeach
        </div>

        <div class="grid grid-cols-[1fr_1fr_1fr_auto] items-stretch min-h-[88px]">
            <div class="relative border-r border-[var(--color-border)]">
                <label class="fs-search-cell" for="search-location">
                    <span class="text-[11px] font-semibold uppercase tracking-wide text-[var(--color-muted)]">Location</span>
                    <input
                        id="search-location"
                        type="text"
                        name="search"
                        value="{{ $currentSearch }}"
                        placeholder="City or area"
                        autocomplete="off"
                        role="combobox"
                        aria-autocomplete="list"
                        aria-expanded="false"
                        aria-controls="location-listbox"
                        data-location-input
                        class="mt-1 w-full bg-transparent text-[15px] font-medium text-[var(--color-ink)] outline-none placeholder:text-[var(--color-muted)]"
                    >
                </label>
                <ul
                    id="location-listbox"
                    role="listbox"
                    data-location-list
                    hidden
                    class="fs-search-popover absolute left-0 right-0 top-full mt-2 z-40 max-h-80 overflow-y-auto bg-white border border-[var(--color-border)] rounded-[var(--radius-card)] shadow-[0_12px_32px_rgba(13,29,25,.12)] py-1"
                ></ul>
            </div>

            <div class="relative border-r border-[var(--color-border)]">
                <input type="hidden" name="type" value="{{ $currentType }}">
                <button type="button" class="fs-search-cell" data-type-trigger aria-expanded="false" aria-haspopup="listbox" aria-controls="type-popover">
                    <span class="text-[11px] font-semibold uppercase tracking-wide text-[var(--color-muted)]">Property type</span>
                    <span class="mt-1 flex items-center justify-between gap-2 text-[15px] font-medium text-[var(--color-ink)]">
                        <span data-type-label>{{ $typeLabel }}</span>
                        <svg class="w-4 h-4 text-[var(--color-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </button>
                <div id="type-popover" data-type-popover data-popover hidden class="fs-search-popover absolute left-0 right-0 top-full mt-2 z-40 bg-white border border-[var(--color-border)] rounded-[var(--radius-card)] shadow-[0_12px_32px_rgba(13,29,25,.12)] p-3">
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2 rounded-[var(--radius-input)] hover:bg-[var(--color-ivory-50)] cursor-pointer">
                            <input type="radio" name="type_ui" value="" data-label="Any type" @checked($currentType === '') class="accent-[var(--color-forest)]">
                            <span class="text-sm">Any</span>
                        </label>
                        @foreach($types as $t)
                            <label class="flex items-center gap-2 p-2 rounded-[var(--radius-input)] hover:bg-[var(--color-ivory-50)] cursor-pointer">
                                <input type="radio" name="type_ui" value="{{ $t['slug'] }}" data-label="{{ $t['name'] }}" @checked($currentType === $t['slug']) class="accent-[var(--color-forest)]">
                                <svg class="w-4 h-4 text-[var(--color-forest)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $icons[$t['slug']] ?? $icons['house'] }}"/></svg>
                                <span class="text-sm">{{ $t['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="relative border-r border-[var(--color-border)]">
                <input type="hidden" name="price" value="{{ $currentPrice }}">
                <button type="button" class="fs-search-cell" data-budget-trigger aria-expanded="false" aria-controls="budget-popover">
                    <span class="text-[11px] font-semibold uppercase tracking-wide text-[var(--color-muted)]">Budget</span>
                    <span class="mt-1 flex items-center justify-between gap-2 text-[15px] font-medium text-[var(--color-ink)]">
                        <span data-budget-label>{{ $budgetLabel }}</span>
                        <svg class="w-4 h-4 text-[var(--color-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </button>
                <div id="budget-popover" data-budget-popover data-popover hidden class="fs-search-popover absolute right-0 left-0 top-full mt-2 z-40 bg-white border border-[var(--color-border)] rounded-[var(--radius-card)] shadow-[0_12px_32px_rgba(13,29,25,.12)] p-4 min-w-[280px]">
                    <div class="grid grid-cols-2 gap-3">
                        <label class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">
                            Min ({{ $currency }})
                            <input type="text" inputmode="numeric" data-budget-min class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm text-[var(--color-ink)]" placeholder="0">
                        </label>
                        <label class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">
                            Max ({{ $currency }})
                            <input type="text" inputmode="numeric" data-budget-max class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm text-[var(--color-ink)]" placeholder="Any">
                        </label>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($presets as $preset)
                            <button
                                type="button"
                                data-budget-preset
                                data-label="{{ $preset['label'] }}"
                                data-price="{{ $preset['price'] }}"
                                data-min="{{ $preset['min'] }}"
                                data-max="{{ $preset['max'] }}"
                                class="h-9 px-3 rounded-full border border-[var(--color-border)] text-xs font-semibold text-[var(--color-forest)] hover:bg-[var(--color-ivory-50)]"
                            >{{ $preset['label'] }}</button>
                        @endforeach
                    </div>
                    <div class="mt-4 flex gap-2 justify-end">
                        <button type="button" data-budget-clear class="h-10 px-4 text-sm font-semibold text-[var(--color-muted)]">Clear</button>
                        <button type="button" data-budget-apply class="h-10 px-4 rounded-[var(--radius-button)] bg-[var(--color-forest)] text-[var(--color-ivory)] text-sm font-semibold">Apply</button>
                    </div>
                </div>
            </div>

            <div class="flex items-center p-3">
                <button type="submit" class="inline-flex items-center justify-center gap-2 h-14 px-6 rounded-[var(--radius-button)] bg-[var(--color-forest)] text-[var(--color-ivory)] text-sm font-semibold hover:bg-[var(--color-forest-shadow)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)] fs-btn-motion">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                    Search
                </button>
            </div>
        </div>
    </form>

    {{-- Mobile sheet --}}
    <div data-sheet-backdrop hidden class="lg:hidden fixed inset-0 z-[70] bg-[rgba(13,29,25,.45)]"></div>
    <div
        id="property-search-sheet"
        data-sheet
        hidden
        role="dialog"
        aria-modal="true"
        aria-label="Search properties"
        class="lg:hidden fs-sheet fixed inset-x-0 bottom-0 z-[80] max-h-[92svh] overflow-y-auto bg-white rounded-t-[16px] border-t border-[var(--color-border)] shadow-[0_-8px_32px_rgba(13,29,25,.18)]"
    >
        <div class="sticky top-0 z-10 flex items-center justify-between px-4 h-14 border-b border-[var(--color-border)] bg-white">
            <p class="text-sm font-semibold text-[var(--color-ink)]">Search</p>
            <button type="button" data-sheet-close class="inline-flex items-center justify-center w-11 h-11 rounded-[var(--radius-button)] border border-[var(--color-border)]" aria-label="Close search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('properties') }}" method="GET" class="p-4 space-y-4 pb-28">
            <div class="flex gap-1 border-b border-[var(--color-border)]" role="tablist">
                @foreach($purposes as $value => $meta)
                    <label class="px-3 h-10 inline-flex items-center text-sm font-semibold text-[var(--color-muted)] has-[:checked]:text-[var(--color-forest)] has-[:checked]:shadow-[inset_0_-2px_0_var(--color-gold-dark)]">
                        <input type="radio" name="purpose" value="{{ $value }}" class="sr-only" @checked($currentPurpose === $value)>
                        {{ $meta['label'] }}
                    </label>
                @endforeach
            </div>
            <x-input label="Location" name="search" :value="$currentSearch" placeholder="City or area" />
            <x-select
                label="Property type"
                name="type"
                :options="collect($types)->pluck('name', 'slug')->all()"
                :selected="$currentType"
                placeholder="Any type"
            />
            <x-select
                label="Budget"
                name="price"
                :options="collect($presets)->filter(fn ($p) => $p['price'])->mapWithKeys(fn ($p) => [$p['price'] => $p['label']])->all()"
                :selected="$currentPrice"
                placeholder="Any budget"
            />
            <div class="fixed inset-x-0 bottom-0 p-4 bg-white border-t border-[var(--color-border)]">
                <button type="submit" class="w-full h-14 rounded-[var(--radius-button)] bg-[var(--color-forest)] text-[var(--color-ivory)] text-sm font-semibold">Search</button>
            </div>
        </form>
    </div>

    {{-- No-JS fallback (visible only without JS via CSS if needed — native selects already in mobile form; desktop gets a simple fallback) --}}
    <noscript>
        <form action="{{ route('properties') }}" method="GET" class="mt-3 grid gap-3 p-4 fs-search-card lg:grid-cols-4">
            <x-input label="Location" name="search" :value="$currentSearch" />
            <x-select label="Property type" name="type" :options="collect($types)->pluck('name', 'slug')->all()" :selected="$currentType" placeholder="Any" />
            <x-select label="Budget" name="price" :options="['0-30000000'=>'Under 3 Cr','30000001-60000000'=>'3–6 Cr','60000001-1000000000'=>'6 Cr+']" :selected="$currentPrice" placeholder="Any" />
            <x-button type="submit" variant="primary" class="w-full">Search</x-button>
        </form>
    </noscript>
</div>
