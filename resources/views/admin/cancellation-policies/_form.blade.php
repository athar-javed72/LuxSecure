@php
    $rows = old('rules');
    if ($rows === null) {
        $rows = ($policy?->rules ?? collect())->map(fn ($r) => [
            'id' => $r->id,
            'days_before_min' => $r->days_before_min,
            'refund_percent' => $r->refund_percent,
            'fee_fixed' => $r->fee_fixed,
            'sort_order' => $r->sort_order,
        ])->values()->all();
    }
    if ($rows === []) {
        $rows = [['days_before_min' => 0, 'refund_percent' => 0, 'fee_fixed' => '', 'sort_order' => 0]];
    }
@endphp

<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-input label="Name *" name="name" :value="old('name', $policy?->name)" required />
        <x-input label="Slug" name="slug" :value="old('slug', $policy?->slug)" placeholder="auto from name if empty" />
        <x-select label="Country" name="country_id" :options="$countries->all()" :selected="old('country_id', $policy?->country_id)" placeholder="Global" />
        <x-select label="Organization" name="organization_id" :options="$organizations->all()" :selected="old('organization_id', $policy?->organization_id)" placeholder="None" />
    </div>
    <div>
        <label class="block text-xs font-semibold text-fs-primary mb-1" for="description">Description</label>
        <textarea id="description" name="description" rows="3" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('description', $policy?->description) }}</textarea>
    </div>
    <div class="flex flex-wrap gap-4">
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="hidden" name="is_default" value="0">
            <input type="checkbox" name="is_default" value="1" {{ old('is_default', $policy?->is_default ?? false) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
            <span class="text-sm text-fs-primary">Default</span>
        </label>
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $policy?->is_active ?? true) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
            <span class="text-sm text-fs-primary">Active</span>
        </label>
    </div>

    <div class="pt-4 border-t border-black/5 space-y-3">
        <h3 class="text-sm font-semibold text-fs-primary">Rules</h3>
        @foreach ($rows as $i => $row)
            <div class="rounded-md border border-black/5 p-4 grid grid-cols-1 md:grid-cols-4 gap-3 bg-fs-surface/40">
                <input type="hidden" name="rules[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">
                <x-input label="Days before (min)" :name="'rules['.$i.'][days_before_min]'" type="number" :value="$row['days_before_min'] ?? 0" required />
                <x-input label="Refund %" :name="'rules['.$i.'][refund_percent]'" type="number" :value="$row['refund_percent'] ?? 0" step="0.01" required />
                <x-input label="Fixed fee" :name="'rules['.$i.'][fee_fixed]'" type="number" :value="$row['fee_fixed'] ?? ''" step="0.01" />
                <x-input label="Sort" :name="'rules['.$i.'][sort_order]'" type="number" :value="$row['sort_order'] ?? $i" />
            </div>
        @endforeach
        <p class="text-xs text-fs-muted">Add more rules by saving, then editing again — or submit extra rows named rules[n].*</p>
    </div>
</div>
