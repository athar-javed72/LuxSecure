@extends('admin.layout')

@section('title', 'Add room type')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Room types', 'href' => route('admin.room-types.index')],
    ['label' => 'Create'],
]" />

<x-page-header title="Add room type" />

<x-panel class="max-w-2xl">
    <form method="POST" action="{{ route('admin.room-types.store') }}" class="space-y-4">
        @csrf
        <x-select label="Hotel listing" name="listing_id" :options="$listings->mapWithKeys(fn ($l) => [$l->id => $l->title])->all()" :selected="old('listing_id')" required />
        <x-input label="Name" name="name" :value="old('name')" required />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Capacity" name="capacity" type="number" :value="old('capacity', 2)" required />
            <x-input label="Base price / night" name="base_price" type="number" step="0.01" :value="old('base_price')" required />
        </div>
        <x-input label="Amenities (comma separated)" name="amenities_text" :value="old('amenities_text')" placeholder="WiFi, AC, Mini bar" />
        <div class="flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">Create</x-button>
            <x-button variant="ghost" href="{{ route('admin.room-types.index') }}">Cancel</x-button>
        </div>
    </form>
</x-panel>
@endsection

@push('scripts')
<script>
document.querySelector('form')?.addEventListener('submit', function (e) {
    const text = this.querySelector('[name="amenities_text"]');
    if (!text) return;
    const values = text.value.split(',').map(s => s.trim()).filter(Boolean);
    values.forEach((v, i) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `amenities[${i}]`;
        input.value = v;
        this.appendChild(input);
    });
});
</script>
@endpush
