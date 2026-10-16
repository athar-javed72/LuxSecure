@php
    use App\Support\Media;
    $featuredUrl = filled($property?->featured_image) ? Media::url($property->featured_image) : '';
@endphp

<div
    class="grid grid-cols-1 md:grid-cols-2 gap-4"
    x-data="{
        featuredPreview: null,
        galleryPreviews: [],
        featuredObjectUrl: null,
        galleryObjectUrls: [],
        onFeaturedChange(event) {
            if (this.featuredObjectUrl) {
                URL.revokeObjectURL(this.featuredObjectUrl);
                this.featuredObjectUrl = null;
            }
            const file = event.target.files && event.target.files[0];
            if (!file) {
                this.featuredPreview = null;
                return;
            }
            this.featuredObjectUrl = URL.createObjectURL(file);
            this.featuredPreview = this.featuredObjectUrl;
        },
        onGalleryChange(event) {
            this.galleryObjectUrls.forEach((url) => URL.revokeObjectURL(url));
            this.galleryObjectUrls = [];
            this.galleryPreviews = [];
            const files = event.target.files ? Array.from(event.target.files) : [];
            files.forEach((file) => {
                const url = URL.createObjectURL(file);
                this.galleryObjectUrls.push(url);
                this.galleryPreviews.push(url);
            });
        },
        destroy() {
            if (this.featuredObjectUrl) URL.revokeObjectURL(this.featuredObjectUrl);
            this.galleryObjectUrls.forEach((url) => URL.revokeObjectURL(url));
        }
    }"
>
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
                  class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('description', $property?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </div>
    <x-input label="Bedrooms" name="bedrooms" type="number" :value="old('bedrooms', $property?->bedrooms)" />
    <x-input label="Bathrooms" name="bathrooms" type="number" :value="old('bathrooms', $property?->bathrooms)" />

    <div class="md:col-span-2 space-y-3">
        <label for="featured_image" class="block text-xs font-semibold text-fs-primary mb-1">Featured image</label>
        @if($featuredUrl !== '')
            <div class="flex items-start gap-3">
                <img
                    src="{{ $featuredUrl }}"
                    alt="Current featured image for {{ $property->title }}"
                    width="160"
                    height="120"
                    class="w-40 h-28 object-cover rounded-sm border border-black/10 bg-fs-surface"
                >
                <p class="text-xs text-fs-muted">Current featured image. Upload a new file to replace it.</p>
            </div>
        @endif
        <input
            id="featured_image"
            type="file"
            name="featured_image"
            accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
            class="w-full h-12 px-3 rounded-sm border border-dashed border-black/15 bg-white text-sm text-fs-secondary file:mr-3 file:py-2 file:px-3 file:rounded-sm file:border-0 file:bg-fs-brand-light file:text-fs-brand file:text-xs file:font-semibold"
            @change="onFeaturedChange($event)"
        >
        <template x-if="featuredPreview">
            <div class="flex items-start gap-3">
                <img :src="featuredPreview" alt="Preview of new featured image upload" width="160" height="120" class="w-40 h-28 object-cover rounded-sm border border-black/10 bg-fs-surface">
                <p class="text-xs text-fs-muted">New featured image preview</p>
            </div>
        </template>
        @error('featured_image')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2 space-y-3">
        <label for="images" class="block text-xs font-semibold text-fs-primary mb-1">More images</label>
        @if($property && $property->relationLoaded('images') && $property->images->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach($property->images as $image)
                    @php $galleryUrl = Media::url($image->path); @endphp
                    @if($galleryUrl !== '')
                        <img
                            src="{{ $galleryUrl }}"
                            alt="Gallery image {{ $loop->iteration }} for {{ $property->title }}"
                            width="96"
                            height="72"
                            class="w-24 h-[72px] object-cover rounded-sm border border-black/10 bg-fs-surface"
                        >
                    @endif
                @endforeach
            </div>
            <p class="text-xs text-fs-muted">Existing gallery images. New uploads are added alongside these.</p>
        @endif
        <input
            id="images"
            type="file"
            name="images[]"
            accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
            multiple
            class="w-full h-12 px-3 rounded-sm border border-dashed border-black/15 bg-white text-sm text-fs-secondary file:mr-3 file:py-2 file:px-3 file:rounded-sm file:border-0 file:bg-fs-surface file:text-fs-secondary file:text-xs file:font-semibold"
            @change="onGalleryChange($event)"
        >
        <div class="flex flex-wrap gap-2" x-show="galleryPreviews.length" x-cloak>
            <template x-for="(src, index) in galleryPreviews" :key="index">
                <img :src="src" :alt="'Preview of new gallery image ' + (index + 1)" width="96" height="72" class="w-24 h-[72px] object-cover rounded-sm border border-black/10 bg-fs-surface">
            </template>
        </div>
        @error('images.*')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $property?->is_active ?? true) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
            <span class="text-sm text-fs-primary">Active (visible on site)</span>
        </label>
    </div>
</div>
