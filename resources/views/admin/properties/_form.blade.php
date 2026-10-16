<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <x-input label="Title *" name="title" :value="old('title', $property?->title)" placeholder="e.g. Modern Villa in DHA" required />
    </div>
    <div class="md:col-span-2">
        <x-input label="Location *" name="location" :value="old('location', $property?->location)" placeholder="e.g. DHA Phase 6, Karachi" required />
    </div>
    <x-select
        label="Type *"
        name="type"
        :options="collect($types)->mapWithKeys(fn ($t) => [$t => $t])->all()"
        :selected="old('type', $property?->type)"
        required
    />
    <x-input label="Price (PKR) *" name="price" type="number" :value="old('price', $property?->price)" required />
    <div class="md:col-span-2">
        <label for="description" class="block text-xs font-semibold text-fs-primary mb-1">Description</label>
        <textarea id="description" name="description" rows="4" placeholder="Brief description..."
                  class="w-full px-4 py-3 rounded-sm border border-slate-200 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('description', $property?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </div>
    <x-input label="Bedrooms" name="bedrooms" type="number" :value="old('bedrooms', $property?->bedrooms)" />
    <x-input label="Bathrooms" name="bathrooms" type="number" :value="old('bathrooms', $property?->bathrooms)" />
    <div>
        <label for="featured_image" class="block text-xs font-semibold text-fs-primary mb-1">Featured image</label>
        <input id="featured_image" type="file" name="featured_image" accept="image/*"
               class="w-full h-12 px-3 rounded-sm border border-dashed border-slate-300 bg-white text-sm text-fs-secondary file:mr-3 file:py-2 file:px-3 file:rounded-sm file:border-0 file:bg-fs-brand-light file:text-fs-brand file:text-xs file:font-semibold">
        @error('featured_image')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="images" class="block text-xs font-semibold text-fs-primary mb-1">More images</label>
        <input id="images" type="file" name="images[]" accept="image/*" multiple
               class="w-full h-12 px-3 rounded-sm border border-dashed border-slate-300 bg-white text-sm text-fs-secondary file:mr-3 file:py-2 file:px-3 file:rounded-sm file:border-0 file:bg-slate-100 file:text-fs-secondary file:text-xs file:font-semibold">
        @error('images.*')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $property?->is_active ?? true) ? 'checked' : '' }} class="rounded-sm border-slate-300 text-fs-brand focus:ring-fs-brand">
            <span class="text-sm text-fs-primary">Active (visible on site)</span>
        </label>
    </div>
</div>
