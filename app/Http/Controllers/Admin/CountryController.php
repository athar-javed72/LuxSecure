<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountrySetting;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CountryController extends Controller
{
    private const SETTING_KEYS = [
        'site.phone' => ['label' => 'Phone', 'type' => 'string'],
        'site.email' => ['label' => 'Email', 'type' => 'string'],
        'site.hours' => ['label' => 'Hours', 'type' => 'string'],
        'site.address' => ['label' => 'Address', 'type' => 'text'],
        'site.support_email' => ['label' => 'Support email', 'type' => 'string'],
        'commerce.commission_hotel' => ['label' => 'Hotel commission (%)', 'type' => 'number'],
        'commerce.commission_rent' => ['label' => 'Rent commission (%)', 'type' => 'number'],
        'commerce.commission_sale' => ['label' => 'Sale commission (%)', 'type' => 'number'],
        'commerce.commission_rate' => ['label' => 'Default commission (%)', 'type' => 'number'],
        'commerce.tax_rate' => ['label' => 'Tax rate (%)', 'type' => 'number'],
        'social.facebook' => ['label' => 'Facebook', 'type' => 'string'],
        'social.instagram' => ['label' => 'Instagram', 'type' => 'string'],
    ];

    public function index(): View
    {
        Gate::authorize('countries.view');

        $countries = Country::query()->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('admin.countries.index', compact('countries'));
    }

    public function create(): View
    {
        Gate::authorize('countries.create');

        return view('admin.countries.create', [
            'currencies' => Currency::query()->where('is_active', true)->orderBy('code')->pluck('code', 'code'),
            'languages' => Language::query()->where('is_active', true)->orderBy('sort_order')->pluck('name', 'code'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('countries.create');

        $data = $this->validated($request);
        Country::create($data);

        return redirect()->route('admin.countries.index')->with('success', 'Country created.');
    }

    public function edit(Country $country): View
    {
        Gate::authorize('countries.update');

        return view('admin.countries.edit', [
            'country' => $country,
            'currencies' => Currency::query()->where('is_active', true)->orderBy('code')->pluck('code', 'code'),
            'languages' => Language::query()->where('is_active', true)->orderBy('sort_order')->pluck('name', 'code'),
        ]);
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        Gate::authorize('countries.update');

        $country->update($this->validated($request, $country));

        return redirect()->route('admin.countries.index')->with('success', 'Country updated.');
    }

    public function settings(Country $country): View
    {
        Gate::authorize('countries.view');

        $existing = $country->settings()->pluck('value', 'key');
        $settings = collect(self::SETTING_KEYS)->map(fn ($meta, $key) => [
            'key' => $key,
            'label' => $meta['label'],
            'type' => $meta['type'],
            'value' => $existing[$key] ?? '',
        ]);

        return view('admin.countries.settings', compact('country', 'settings'));
    }

    public function updateSettings(Request $request, Country $country): RedirectResponse
    {
        Gate::authorize('countries.update');

        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:2000'],
        ]);

        foreach (self::SETTING_KEYS as $key => $meta) {
            if (! array_key_exists($key, $data['settings'])) {
                continue;
            }
            CountrySetting::setValue(
                $country->id,
                $key,
                $data['settings'][$key] ?? '',
                $meta['type'],
                $meta['label']
            );
        }

        return back()->with('success', 'Country settings saved.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Country $country = null): array
    {
        $data = $request->validate([
            'iso2' => ['required', 'string', 'size:2', 'unique:countries,iso2,'.($country?->id ?? 'NULL')],
            'name' => ['required', 'string', 'max:120'],
            'currency_code' => ['required', 'string', 'size:3'],
            'default_locale' => ['required', 'string', 'max:12'],
            'locales_csv' => ['nullable', 'string', 'max:120'],
            'timezone' => ['required', 'string', 'max:64'],
            'phone_code' => ['nullable', 'string', 'max:8'],
            'phone_regex' => ['nullable', 'string', 'max:255'],
            'tax_name' => ['nullable', 'string', 'max:120'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_needs_review' => ['sometimes', 'boolean'],
            'invoice_prefix' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['iso2'] = strtoupper($data['iso2']);
        $data['currency_code'] = strtoupper($data['currency_code']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['tax_needs_review'] = $request->boolean('tax_needs_review');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['tax_percent'] = $data['tax_percent'] ?? 0;
        $localesCsv = (string) ($data['locales_csv'] ?? '');
        unset($data['locales_csv']);
        $data['locales'] = $localesCsv !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $localesCsv))))
            : [$data['default_locale']];

        return $data;
    }
}
