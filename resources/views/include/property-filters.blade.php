@php $idPrefix = $idPrefix ?? 'filter'; @endphp
<form method="GET" action="{{ route('properties') }}" class="space-y-4">
    <x-input label="Location" name="search" :id="$idPrefix . '-search'" :value="request('search')" placeholder="City or area" />
    <x-select
        label="Property Type"
        name="type"
        :id="$idPrefix . '-type'"
        :options="collect(\App\Models\Property::TYPES)->mapWithKeys(fn ($t) => [$t => $t])->all()"
        :selected="request('type')"
        placeholder="All types"
    />
    <x-select
        label="Budget"
        name="price"
        :id="$idPrefix . '-price'"
        :options="[
            '0-30000000' => 'Below 3 Crore',
            '30000001-60000000' => '3–6 Crore',
            '60000001-100000000' => '6–10 Crore',
            '100000001-1000000000' => '10 Crore+',
        ]"
        :selected="request('price')"
        placeholder="Any budget"
    />
    @if(request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif
    <x-button type="submit" variant="primary" class="w-full">Apply filters</x-button>
    <x-button variant="ghost" href="{{ route('properties') }}" class="w-full">Reset</x-button>
</form>
