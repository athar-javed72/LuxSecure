@php
    $rows = old('attributes');
    if ($rows === null) {
        $rows = ($listingType?->attributes ?? collect())->map(fn ($a) => [
            'id' => $a->id,
            'key' => $a->key,
            'label' => $a->label,
            'input_type' => $a->input_type,
            'options' => is_array($a->options) ? implode(', ', $a->options) : '',
            'is_required' => $a->is_required,
            'is_filterable' => $a->is_filterable,
            'sort_order' => $a->sort_order,
        ])->values()->all();
    }
    if ($rows === []) {
        $rows = [['key' => '', 'label' => '', 'input_type' => 'text', 'options' => '', 'is_required' => false, 'is_filterable' => false, 'sort_order' => 0]];
    }
@endphp

<div
    class="space-y-4"
    x-data="listingTypeForm({ rows: @js($rows) })"
>
    <x-input label="Name *" name="name" :value="old('name', $listingType?->name)" required />
    <x-input label="Slug" name="slug" :value="old('slug', $listingType?->slug)" placeholder="auto from name if empty" />
    <x-input label="Icon" name="icon" :value="old('icon', $listingType?->icon)" placeholder="optional icon key" />
    <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $listingType?->is_active ?? true) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
        <span class="text-sm text-fs-primary">Active</span>
    </label>

    <div class="pt-4 border-t border-black/5">
        <div class="flex items-center justify-between gap-2 mb-3">
            <h3 class="text-sm font-semibold text-fs-primary">Attributes</h3>
            <x-button type="button" variant="secondary" @click="addRow()">Add attribute</x-button>
        </div>
        <div class="space-y-4">
            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-md border border-black/5 p-4 grid grid-cols-1 md:grid-cols-2 gap-3 bg-fs-surface/40">
                    <input type="hidden" :name="'attributes[' + index + '][id]'" :value="row.id || ''">
                    <div>
                        <label class="block text-xs font-semibold text-fs-primary mb-1">Key *</label>
                        <input type="text" class="w-full h-11 px-3 rounded-sm border border-black/10 bg-white text-sm" :name="'attributes[' + index + '][key]'" x-model="row.key" placeholder="bedrooms">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-fs-primary mb-1">Label *</label>
                        <input type="text" class="w-full h-11 px-3 rounded-sm border border-black/10 bg-white text-sm" :name="'attributes[' + index + '][label]'" x-model="row.label" placeholder="Bedrooms">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-fs-primary mb-1">Input type *</label>
                        <select class="w-full h-11 px-3 rounded-sm border border-black/10 bg-white text-sm" :name="'attributes[' + index + '][input_type]'" x-model="row.input_type">
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="select">Select</option>
                            <option value="boolean">Boolean</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-fs-primary mb-1">Options (comma-separated)</label>
                        <input type="text" class="w-full h-11 px-3 rounded-sm border border-black/10 bg-white text-sm" :name="'attributes[' + index + '][options]'" x-model="row.options" placeholder="1, 2, 3, 4">
                    </div>
                    <label class="inline-flex items-center gap-2 min-h-11">
                        <input type="hidden" :name="'attributes[' + index + '][is_required]'" value="0">
                        <input type="checkbox" value="1" class="rounded-sm border-black/20 text-fs-brand" :name="'attributes[' + index + '][is_required]'" :checked="row.is_required" @change="row.is_required = $event.target.checked">
                        <span class="text-sm">Required</span>
                    </label>
                    <label class="inline-flex items-center gap-2 min-h-11">
                        <input type="hidden" :name="'attributes[' + index + '][is_filterable]'" value="0">
                        <input type="checkbox" value="1" class="rounded-sm border-black/20 text-fs-brand" :name="'attributes[' + index + '][is_filterable]'" :checked="row.is_filterable" @change="row.is_filterable = $event.target.checked">
                        <span class="text-sm">Filterable</span>
                    </label>
                    <div class="md:col-span-2">
                        <x-button type="button" variant="ghost" @click="removeRow(index)">Remove</x-button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
