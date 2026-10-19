<div class="space-y-4">
    <x-input label="Name *" name="name" :value="old('name', $organization?->name)" required />
    <x-input label="Slug" name="slug" :value="old('slug', $organization?->slug)" placeholder="auto from name if empty" />
    <x-input label="Logo path" name="logo" :value="old('logo', $organization?->logo)" />
    <div>
        <label class="block text-xs font-semibold text-fs-primary mb-1" for="description">Description</label>
        <textarea id="description" name="description" rows="4" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('description', $organization?->description) }}</textarea>
    </div>
    <x-location-picker
        class="md:col-span-2"
        :countries="$countries"
        country-name="country_id"
        city-name="city_id"
        :country-id="old('country_id', $organization?->country_id)"
        :required="false"
        :allow-empty-country="true"
        city-label-text="City (optional)"
    />
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-select label="Owner" name="owner_id" :options="$owners->all()" :selected="old('owner_id', $organization?->owner_id)" placeholder="None" />
    </div>
    <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $organization?->is_active ?? true) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
        <span class="text-sm text-fs-primary">Active</span>
    </label>
</div>
