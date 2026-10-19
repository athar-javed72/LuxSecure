@props([
    'countries' => collect(),
    'countryName' => 'country_id',
    'cityName' => 'city_id',
    'countryId' => null,
    'cityId' => null,
    'cityLabel' => null,
    'required' => false,
    'countryLabel' => 'Country',
    'cityLabelText' => 'City',
    'citiesUrlTemplate' => null,
    'allowEmptyCountry' => true,
])

@php
    $selectedCountry = old($countryName, $countryId);
    $selectedCity = old($cityName, $cityId);
    $selectedCityLabel = $cityLabel;
    if ($selectedCity && ! $selectedCityLabel) {
        $selectedCityLabel = optional(\App\Models\City::query()->find($selectedCity))->displayLabel();
    }
    $citiesUrlTemplate = $citiesUrlTemplate ?: url('/countries/__COUNTRY__/cities');
    $config = [
        'countryName' => $countryName,
        'cityName' => $cityName,
        'countryId' => $selectedCountry !== null && $selectedCountry !== '' ? (int) $selectedCountry : null,
        'cityId' => $selectedCity !== null && $selectedCity !== '' ? (int) $selectedCity : null,
        'cityLabel' => $selectedCityLabel,
        'citiesUrlTemplate' => $citiesUrlTemplate,
        'required' => (bool) $required,
        'debounceMs' => 300,
        'minChars' => 2,
    ];
@endphp

<div
    {{ $attributes->merge(['class' => 'grid gap-4 sm:grid-cols-2']) }}
    x-data="locationPicker"
    data-alpine-config="{{ json_encode($config, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
>
    <div>
        <label for="{{ $countryName }}" class="block text-xs font-semibold text-fs-primary mb-1">{{ $countryLabel }}@if($required) * @endif</label>
        <select
            name="{{ $countryName }}"
            id="{{ $countryName }}"
            class="w-full h-12 px-4 rounded-sm border border-slate-200 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
            x-model="countryId"
            @change="onCountryChange()"
            @if($required) required @endif
        >
            @if($allowEmptyCountry)
                <option value="">Select country</option>
            @endif
            @foreach($countries as $c)
                @php
                    $cid = is_array($c) ? $c['id'] : $c->id;
                    $cname = is_array($c) ? $c['name'] : $c->name;
                @endphp
                <option value="{{ $cid }}" @selected((string) $selectedCountry === (string) $cid)>{{ $cname }}</option>
            @endforeach
        </select>
        @error($countryName)
            <p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>
        @enderror
    </div>

    <div class="relative">
        <label for="{{ $cityName }}_search" class="block text-xs font-semibold text-fs-primary mb-1">{{ $cityLabelText }}@if($required) * @endif</label>
        <input type="hidden" name="{{ $cityName }}" x-model="cityId">
        <input
            type="search"
            id="{{ $cityName }}_search"
            class="w-full h-12 px-4 rounded-sm border border-slate-200 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
            placeholder="Search city…"
            autocomplete="off"
            x-model="cityQuery"
            @input="onCityQuery()"
            @focus="onCityFocus()"
            @keydown.escape="closeDropdown()"
            :disabled="cityDisabled"
        >
        <div
            x-show="showDropdown"
            x-cloak
            class="absolute z-20 mt-1 w-full max-h-56 overflow-auto rounded-sm border border-slate-200 bg-white shadow-sm"
            @click.outside="closeDropdown()"
        >
            <p x-show="loading" class="px-3 py-2 text-xs text-fs-muted">Loading…</p>
            <div x-show="showError" class="px-3 py-2 text-xs text-fs-danger">
                <span x-text="error"></span>
                <button type="button" class="ml-2 underline" @click="loadCities()">Retry</button>
            </div>
            <p x-show="showEmpty" class="px-3 py-2 text-xs text-fs-muted">No cities found</p>
            <template x-for="city in results" :key="city.id">
                <button
                    type="button"
                    class="block w-full text-left px-3 py-2 text-sm hover:bg-fs-surface"
                    @click="selectCity(city)"
                    x-text="city.label"
                ></button>
            </template>
        </div>
        <p x-show="hasSelection" class="mt-1 text-xs text-fs-muted" x-text="selectedHint"></p>
        @error($cityName)
            <p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>
        @enderror
    </div>
</div>
