<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-input label="Code *" name="code" :value="old('code', $language?->code)" required />
        <x-input label="Name *" name="name" :value="old('name', $language?->name)" required />
        <x-input label="Native name" name="native_name" :value="old('native_name', $language?->native_name)" />
        <x-input label="Sort order" name="sort_order" type="number" :value="old('sort_order', $language?->sort_order ?? 0)" />
    </div>
    <div class="flex flex-wrap gap-4">
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="hidden" name="is_rtl" value="0">
            <input type="checkbox" name="is_rtl" value="1" {{ old('is_rtl', $language?->is_rtl ?? false) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
            <span class="text-sm text-fs-primary">RTL</span>
        </label>
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $language?->is_active ?? true) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
            <span class="text-sm text-fs-primary">Active</span>
        </label>
    </div>
</div>
