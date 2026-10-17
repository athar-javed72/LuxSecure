@php $profile = $profile ?? null; @endphp
<x-input label="Phone" name="phone" :value="old('phone', $profile->phone ?? '')" />
<x-input label="WhatsApp" name="whatsapp" :value="old('whatsapp', $profile->whatsapp ?? '')" />
<x-input label="License number" name="license_number" :value="old('license_number', $profile->license_number ?? '')" />
<x-input label="Agency" name="agency" :value="old('agency', $profile->agency ?? '')" />
<div>
    <label class="block text-xs font-semibold mb-1">Bio</label>
    <textarea name="bio" rows="4" class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm">{{ old('bio', $profile->bio ?? '') }}</textarea>
</div>
<div>
    <label class="block text-xs font-semibold mb-1">Photo</label>
    <input type="file" name="photo" accept="image/*" class="w-full text-sm">
</div>
<x-input label="Sort order" name="sort_order" type="number" :value="old('sort_order', $profile->sort_order ?? 0)" />
<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_verified" value="1" @checked(old('is_verified', $profile->is_verified ?? false))> Verified</label>
<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $profile->is_featured ?? false))> Featured on home</label>
