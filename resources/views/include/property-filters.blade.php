@php
    $idPrefix = $idPrefix ?? 'filter';
    $listingTypes = $listingTypes ?? \App\Models\ListingType::query()->where('is_active', true)->orderBy('name')->get();
    $cities = $cities ?? \App\Models\City::query()->where('is_active', true)->orderBy('name')->get();
    $filterableAttributes = $filterableAttributes ?? collect();
@endphp
<form method="GET" action="{{ route('properties') }}" class="space-y-4">
    <x-input label="Location" name="search" :id="$idPrefix . '-search'" :value="request('search')" placeholder="City or area" />
    <x-select
        label="Listing Type"
        name="type"
        :id="$idPrefix . '-type'"
        :options="$listingTypes->mapWithKeys(fn ($t) => [$t->slug => $t->name])->all()"
        :selected="request('type')"
        placeholder="All types"
    />
    <x-select
        label="Purpose"
        name="purpose"
        :id="$idPrefix . '-purpose'"
        :options="[
            'sale' => 'For Sale',
            'rent' => 'For Rent',
            'short_stay' => 'Short Stay',
        ]"
        :selected="request('purpose')"
        placeholder="Any purpose"
    />
    <x-select
        label="City"
        name="city_id"
        :id="$idPrefix . '-city'"
        :options="$cities->mapWithKeys(fn ($c) => [$c->id => $c->name])->all()"
        :selected="request('city_id')"
        placeholder="All cities"
    />
    <x-select
        label="Budget"
        name="price"
        :id="$idPrefix . '-price'"
        :options="[
            '0-30000000' => 'Below 3 Crore / mid range',
            '30000001-60000000' => '3–6 Crore',
            '60000001-100000000' => '6–10 Crore',
            '100000001-1000000000' => '10 Crore+',
            '0-100000' => 'Rent/stay under 100k',
            '100001-300000' => 'Rent/stay 100k–300k',
        ]"
        :selected="request('price')"
        placeholder="Any budget"
    />

    @foreach($filterableAttributes as $attr)
        @if($attr->input_type === 'boolean')
            <x-select
                :label="$attr->label"
                :name="'attr_'.$attr->key"
                :id="$idPrefix . '-attr-' . $attr->key"
                :options="['1' => 'Yes', '0' => 'No']"
                :selected="request('attr_'.$attr->key)"
                placeholder="Any"
            />
        @elseif($attr->input_type === 'select')
            <x-select
                :label="$attr->label"
                :name="'attr_'.$attr->key"
                :id="$idPrefix . '-attr-' . $attr->key"
                :options="collect($attr->options ?? [])->mapWithKeys(fn ($v, $k) => [is_int($k) ? $v : $k => is_int($k) ? $v : $v])->all()"
                :selected="request('attr_'.$attr->key)"
                placeholder="Any"
            />
        @else
            <x-input
                :label="$attr->label"
                :name="'attr_'.$attr->key"
                :id="$idPrefix . '-attr-' . $attr->key"
                :type="$attr->input_type === 'number' ? 'number' : 'text'"
                :value="request('attr_'.$attr->key)"
            />
        @endif
    @endforeach

    @if(request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif
    <x-button type="submit" variant="primary" class="w-full">Apply filters</x-button>
    <x-button variant="ghost" href="{{ route('properties') }}" class="w-full">Reset</x-button>
</form>
