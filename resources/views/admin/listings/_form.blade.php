@php
    use App\Support\Media;
    $featuredUrl = filled($listing?->featured_image) ? Media::url($listing->featured_image) : '';
    $selectedType = (string) old('listing_type_id', $listing?->listing_type_id ?? ($listingTypes->first()->id ?? ''));
    $currentAttrs = old('attributes', $listing?->attributeValues() ?? []);
@endphp

<div
    class="grid grid-cols-1 md:grid-cols-2 gap-4"
    x-data="{
        listingTypeId: @js($selectedType),
        types: @js($typesPayload),
        attrValues: @js($currentAttrs),
        featuredPreview: null,
        galleryPreviews: [],
        featuredObjectUrl: null,
        galleryObjectUrls: [],
        get dynamicAttributes() {
            const type = this.types[this.listingTypeId];
            return type ? type.attributes : [];
        },
        optionEntries(options) {
            if (!options) return [];
            if (Array.isArray(options)) {
                return options.map((v) => ({ value: String(v), label: String(v) }));
            }
            return Object.keys(options).map((k) => ({ value: String(k), label: String(options[k]) }));
        },
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
        <x-input label="Title *" name="title" :value="old('title', $listing?->title)" placeholder="e.g. Modern Villa in DHA" required />
    </div>
    <div class="md:col-span-2">
        <x-input label="Location *" name="location" :value="old('location', $listing?->location)" placeholder="e.g. DHA Phase 6, Karachi" required />
    </div>

    <div>
        <label for="listing_type_id" class="block text-xs font-semibold text-fs-primary mb-1">Listing type *</label>
        <select
            id="listing_type_id"
            name="listing_type_id"
            required
            x-model="listingTypeId"
            class="w-full h-12 px-4 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
        >
            <option value="">Select type</option>
            @foreach($listingTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
        @error('listing_type_id')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </div>

    <x-select
        label="Purpose *"
        name="purpose"
        :options="collect($purposes)->mapWithKeys(fn ($p) => [$p => str_replace('_', ' ', ucfirst($p))])->all()"
        :selected="old('purpose', $listing?->purpose ?? 'sale')"
        required
    />
    <x-input label="Price *" name="price" type="number" :value="old('price', $listing?->price)" required />
    <x-select
        label="Price unit *"
        name="price_unit"
        :options="collect($priceUnits)->mapWithKeys(fn ($u) => [$u => str_replace('_', ' ', ucfirst($u))])->all()"
        :selected="old('price_unit', $listing?->price_unit ?? 'total')"
        required
    />
    <x-input label="Currency" name="currency" :value="old('currency', $listing?->currency ?? 'PKR')" />
    <x-select
        label="Status *"
        name="status"
        :options="collect($statuses)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()"
        :selected="old('status', $listing?->status ?? 'published')"
        required
    />
    <x-select
        label="City"
        name="city_id"
        :options="$cities->mapWithKeys(fn ($c) => [$c->id => $c->name])->all()"
        :selected="old('city_id', $listing?->city_id)"
        placeholder="Select city"
    />
    <x-input label="Area size" name="area_size" type="number" :value="old('area_size', $listing?->area_size)" />
    <x-select
        label="Area unit"
        name="area_unit"
        :options="collect($areaUnits)->mapWithKeys(fn ($u) => [$u => str_replace('_', ' ', $u)])->all()"
        :selected="old('area_unit', $listing?->area_unit)"
        placeholder="Unit"
    />
    <x-select
        label="Owner"
        name="owner_id"
        :options="$users->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()"
        :selected="old('owner_id', $listing?->owner_id)"
        placeholder="Optional"
    />
    <x-select
        label="Agent"
        name="agent_id"
        :options="$users->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()"
        :selected="old('agent_id', $listing?->agent_id)"
        placeholder="Optional"
    />

    <div class="md:col-span-2">
        <label for="description" class="block text-xs font-semibold text-fs-primary mb-1">Description</label>
        <textarea id="description" name="description" rows="4" placeholder="Brief description..."
                  class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('description', $listing?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2 rounded-md border border-black/5 bg-fs-surface/60 p-4 space-y-3" x-show="dynamicAttributes.length" x-cloak>
        <h3 class="text-sm font-semibold text-fs-primary">Type attributes</h3>
        <template x-for="attr in dynamicAttributes" :key="attr.key">
            <div>
                <label class="block text-xs font-semibold text-fs-primary mb-1" :for="'attr_' + attr.key">
                    <span x-text="attr.label"></span>
                    <span x-show="attr.is_required"> *</span>
                </label>

                <template x-if="attr.input_type === 'boolean'">
                    <label class="inline-flex items-center gap-2 min-h-11">
                        <input type="hidden" :name="'attributes[' + attr.key + ']'" value="0">
                        <input
                            type="checkbox"
                            value="1"
                            class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"
                            :name="'attributes[' + attr.key + ']'"
                            :id="'attr_' + attr.key"
                            :checked="attrValues[attr.key] == 1 || attrValues[attr.key] === true || attrValues[attr.key] === '1'"
                        >
                        <span class="text-sm text-fs-secondary" x-text="'Yes'"></span>
                    </label>
                </template>

                <template x-if="attr.input_type === 'select'">
                    <select
                        class="w-full h-12 px-4 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
                        :name="'attributes[' + attr.key + ']'"
                        :id="'attr_' + attr.key"
                        :required="attr.is_required"
                    >
                        <option value="">Select</option>
                        <template x-for="opt in optionEntries(attr.options)" :key="opt.value">
                            <option :value="opt.value" :selected="String(attrValues[attr.key] ?? '') === opt.value" x-text="opt.label"></option>
                        </template>
                    </select>
                </template>

                <template x-if="attr.input_type === 'number'">
                    <input
                        type="number"
                        class="w-full h-12 px-4 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
                        :name="'attributes[' + attr.key + ']'"
                        :id="'attr_' + attr.key"
                        :required="attr.is_required"
                        :value="attrValues[attr.key] ?? ''"
                    >
                </template>

                <template x-if="attr.input_type === 'text'">
                    <input
                        type="text"
                        class="w-full h-12 px-4 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
                        :name="'attributes[' + attr.key + ']'"
                        :id="'attr_' + attr.key"
                        :required="attr.is_required"
                        :value="attrValues[attr.key] ?? ''"
                    >
                </template>
            </div>
        </template>
    </div>

    <div class="md:col-span-2 space-y-3">
        <label for="featured_image" class="block text-xs font-semibold text-fs-primary mb-1">Featured image</label>
        @if($featuredUrl !== '')
            <div class="flex items-start gap-3">
                <img src="{{ $featuredUrl }}" alt="Current featured image" width="160" height="120" class="w-40 h-28 object-cover rounded-sm border border-black/10 bg-fs-surface">
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
        @if($listing && $listing->relationLoaded('images') && $listing->images->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach($listing->images as $image)
                    @php $galleryUrl = Media::url($image->path); @endphp
                    @if($galleryUrl !== '')
                        <img src="{{ $galleryUrl }}" alt="Gallery image {{ $loop->iteration }}" width="96" height="72" class="w-24 h-[72px] object-cover rounded-sm border border-black/10 bg-fs-surface">
                    @endif
                @endforeach
            </div>
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
</div>
