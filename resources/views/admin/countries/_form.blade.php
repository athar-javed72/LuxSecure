<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-input label="ISO2 *" name="iso2" :value="old('iso2', $country?->iso2)" maxlength="2" required />
        <x-input label="Name *" name="name" :value="old('name', $country?->name)" required />
        <x-select label="Currency *" name="currency_code" :options="$currencies->all()" :selected="old('currency_code', $country?->currency_code)" required />
        <x-select label="Default locale *" name="default_locale" :options="$languages->all()" :selected="old('default_locale', $country?->default_locale)" required />
        <x-input label="Timezone *" name="timezone" :value="old('timezone', $country?->timezone ?? 'UTC')" required />
        <x-input label="Phone code" name="phone_code" :value="old('phone_code', $country?->phone_code)" />
        <x-input label="Phone regex" name="phone_regex" :value="old('phone_regex', $country?->phone_regex)" />
        <x-input label="Tax name" name="tax_name" :value="old('tax_name', $country?->tax_name)" />
        <x-input label="Tax percent" name="tax_percent" type="number" :value="old('tax_percent', $country?->tax_percent ?? 0)" step="0.0001" />
        <x-input label="Invoice prefix" name="invoice_prefix" :value="old('invoice_prefix', $country?->invoice_prefix)" />
        <x-input label="Sort order" name="sort_order" type="number" :value="old('sort_order', $country?->sort_order ?? 0)" />
        <x-input label="Locales (comma-separated)" name="locales_csv" :value="old('locales_csv', implode(',', $country?->locales ?? []))" placeholder="en,ur" />
    </div>
    <div class="flex flex-wrap gap-4">
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $country?->is_active ?? true) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
            <span class="text-sm text-fs-primary">Active</span>
        </label>
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="hidden" name="tax_needs_review" value="0">
            <input type="checkbox" name="tax_needs_review" value="1" {{ old('tax_needs_review', $country?->tax_needs_review ?? false) ? 'checked' : '' }} class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
            <span class="text-sm text-fs-primary">Tax needs review</span>
        </label>
    </div>
</div>
