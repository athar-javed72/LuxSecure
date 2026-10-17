@php
    $rolesValue = old('for_roles');
    if ($rolesValue === null) {
        $rolesValue = is_array($type?->for_roles) ? implode(', ', $type->for_roles) : '';
    }
@endphp

<div class="space-y-4">
    <x-select label="Country *" name="country_id" :options="$countries->all()" :selected="old('country_id', $type?->country_id ?? $countryId ?? null)" required />
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-input label="Name *" name="name" :value="old('name', $type?->name)" required />
        <x-input label="Code" name="code" :value="old('code', $type?->code)" />
        <x-input label="For roles (comma-separated)" name="for_roles" :value="$rolesValue" placeholder="agent, landlord" />
        <x-input label="Sort order" name="sort_order" type="number" :value="old('sort_order', $type?->sort_order ?? 0)" />
    </div>
    <div class="flex flex-wrap gap-4">
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="hidden" name="is_required" value="0">
            <input type="checkbox" name="is_required" value="1" {{ old('is_required', $type?->is_required ?? false) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
            <span class="text-sm text-fs-primary">Required</span>
        </label>
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $type?->is_active ?? true) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
            <span class="text-sm text-fs-primary">Active</span>
        </label>
    </div>
</div>
