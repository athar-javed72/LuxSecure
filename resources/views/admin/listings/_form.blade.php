@php
    use App\Support\Media;
    $featuredUrl = filled($listing?->featured_image) ? Media::url($listing->featured_image) : '';
    $selectedType = (string) old('listing_type_id', $listing?->listing_type_id ?? ($listingTypes->first()->id ?? ''));
    $currentAttrs = old('attributes', $listing?->attributeValues() ?? []);
@endphp

<div
    class="grid grid-cols-1 md:grid-cols-2 gap-4"
    x-data="listingForm({
        listingTypeId: @js($selectedType),
        types: @js($typesPayload),
        attrValues: @js($currentAttrs)
    })"
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

    <x-panel class="md:col-span-2 bg-fs-surface/60 space-y-3" x-show="dynamicAttributes.length" x-cloak>
        <h3 class="fs-display text-sm font-semibold text-fs-primary">Type attributes</h3>
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
    </x-panel>

    <x-panel class="md:col-span-2 space-y-3">
        <h3 class="fs-display text-sm font-semibold text-fs-primary">Featured image</h3>
        @if($featuredUrl !== '')
            <div class="flex items-start gap-3">
                <img src="{{ $featuredUrl }}" alt="Current featured image" width="160" height="120" class="w-40 h-28 object-cover rounded-sm border border-black/10 bg-fs-surface">
                <p class="text-xs text-fs-muted">Current featured image. Upload a new file to replace it.</p>
            </div>
        @endif
        <x-file-upload
            label="Upload featured image"
            name="featured_image"
            id="featured_image"
            accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
            @change="onFeaturedChange($event)"
        />
        <template x-if="featuredPreview">
            <div class="flex items-start gap-3">
                <img :src="featuredPreview" alt="Preview of new featured image upload" width="160" height="120" class="w-40 h-28 object-cover rounded-sm border border-black/10 bg-fs-surface">
                <p class="text-xs text-fs-muted">New featured image preview</p>
            </div>
        </template>
    </x-panel>

    <x-panel class="md:col-span-2 space-y-3">
        <h3 class="fs-display text-sm font-semibold text-fs-primary">Gallery</h3>
        @if($listing && $listing->relationLoaded('images') && $listing->images->isNotEmpty())
            <div class="space-y-3" x-data="imageOrder({{ $listing->images->pluck('id')->values()->toJson() }})">
                <div class="flex flex-wrap gap-3">
                    @foreach($listing->images as $image)
                        @php $galleryUrl = Media::url($image->path); @endphp
                        <div class="relative w-28 space-y-1">
                            @if($galleryUrl !== '')
                                <img src="{{ $galleryUrl }}" alt="Gallery image {{ $loop->iteration }}" width="112" height="84" class="w-28 h-[84px] object-cover rounded-sm border border-black/10 bg-fs-surface">
                            @endif
                            <div class="flex gap-1">
                                <button type="button" class="text-[10px] text-fs-brand underline" @click="const i = order.indexOf({{ $image->id }}); if (i > 0) { const t = order[i-1]; order[i-1]=order[i]; order[i]=t; }">Up</button>
                                <button type="button" class="text-[10px] text-fs-brand underline" @click="const i = order.indexOf({{ $image->id }}); if (i < order.length-1) { const t = order[i+1]; order[i+1]=order[i]; order[i]=t; }">Down</button>
                            </div>
                            <form method="POST" action="{{ route('admin.listings.images.destroy', [$listing, $image]) }}" onsubmit="return confirm('Delete this image?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] text-fs-danger underline">Delete</button>
                            </form>
                        </div>
                    @endforeach
                </div>
                <form method="POST" action="{{ route('admin.listings.images.reorder', $listing) }}" class="inline">
                    @csrf
                    <template x-for="id in order" :key="id">
                        <input type="hidden" name="order[]" :value="id">
                    </template>
                    <x-button type="submit" variant="ghost">Save gallery order</x-button>
                </form>
            </div>
        @endif
        <x-file-upload
            label="Add gallery images"
            name="images[]"
            id="images"
            accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
            multiple
            @change="onGalleryChange($event)"
        />
        <div class="flex flex-wrap gap-2" x-show="galleryPreviews.length" x-cloak>
            <template x-for="(src, index) in galleryPreviews" :key="index">
                <img :src="src" :alt="'Preview of new gallery image ' + (index + 1)" width="96" height="72" class="w-24 h-[72px] object-cover rounded-sm border border-black/10 bg-fs-surface">
            </template>
        </div>
        @error('images.*')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </x-panel>

    <x-panel class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h3 class="fs-display text-sm font-semibold text-fs-primary mb-3">Video</h3>
            <x-input label="Video URL" name="video_url" type="url" :value="old('video_url', $listing?->video_url)" placeholder="https://..." />
        </div>
        <div>
            <h3 class="fs-display text-sm font-semibold text-fs-primary mb-3">Floor plan</h3>
            @if($listing?->floor_plan_path)
                <p class="mb-2 text-xs text-fs-muted">Current: {{ $listing->floor_plan_path }}</p>
            @endif
            <x-file-upload label="Upload floor plan" name="floor_plan" id="floor_plan" accept=".jpg,.jpeg,.png,.pdf" />
        </div>
    </x-panel>

    <x-panel class="md:col-span-2 space-y-3">
        <h3 class="fs-display text-sm font-semibold text-fs-primary">Private document (title deed / NOC)</h3>
        @if($listing?->private_document_path)
            <p class="text-xs text-fs-muted">Stored on private disk</p>
        @endif
        <x-file-upload label="Upload private document" name="private_document" id="private_document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
    </x-panel>
</div>
