<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-input label="Code *" name="code" :value="old('code', $currency?->code)" maxlength="3" required />
        <x-input label="Symbol *" name="symbol" :value="old('symbol', $currency?->symbol)" required />
        <x-input label="Decimals *" name="decimals" type="number" :value="old('decimals', $currency?->decimals ?? 2)" required />
        <x-input label="Rate to base" name="rate_to_base" type="number" step="0.00000001" :value="old('rate_to_base', $currency?->rate_to_base)" />
    </div>
    <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $currency?->is_active ?? true) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
        <span class="text-sm text-fs-primary">Active</span>
    </label>
</div>
