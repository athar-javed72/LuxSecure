<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountrySetting;
use App\Models\Currency;
use App\Models\Language;
use App\Models\User;
use App\Support\Authorization\CountryAccess;
use App\Support\CountryReadiness;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
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
        'commerce.no_tax' => ['label' => 'No tax (1 = yes)', 'type' => 'string'],
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
        // New countries start inactive; enable via Visibility once readiness passes.
        $data['is_active'] = false;
        $data['wind_down'] = false;
        Country::create($data);

        return redirect()->route('admin.countries.index')->with('success', 'Country created (inactive). Enable it under Visibility when ready.');
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
        $this->assertCountryAccess($request->user(), $country);

        $data = $this->validated($request, $country);
        if (! empty($data['is_active']) && ! $country->is_active) {
            $probe = $country->replicate();
            $probe->fill($data);
            $this->assertReadyToActivate($probe);
        }
        // Visibility changes belong on the dedicated screen for unscoped admins.
        if ($request->user()?->cannot('countries.manage_visibility')
            || ! CountryAccess::allowsGlobal($request->user())) {
            unset($data['is_active']);
        }
        $country->update($data);

        return redirect()->route('admin.countries.index')->with('success', 'Country updated.');
    }

    public function settings(Country $country): View
    {
        Gate::authorize('countries.view');
        $this->assertCountryAccess(request()->user(), $country);

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
        $this->assertCountryAccess($request->user(), $country);

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
        $activating = $request->boolean('is_active', $country?->is_active ?? false);

        $data = $request->validate([
            'iso2' => ['required', 'string', 'size:2', 'unique:countries,iso2,'.($country?->id ?? 'NULL')],
            'iso3' => ['nullable', 'string', 'size:3', 'unique:countries,iso3,'.($country?->id ?? 'NULL')],
            'name' => ['required', 'string', 'max:120'],
            'currency_code' => [$activating ? 'required' : 'nullable', 'string', 'size:3'],
            'default_locale' => [$activating ? 'required' : 'nullable', 'string', 'max:12'],
            'locales_csv' => ['nullable', 'string', 'max:120'],
            'timezone' => [$activating ? 'required' : 'nullable', 'string', 'max:64'],
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
        if (! empty($data['iso3'])) {
            $data['iso3'] = strtoupper($data['iso3']);
        }
        if (! empty($data['currency_code'])) {
            $data['currency_code'] = strtoupper($data['currency_code']);
        }
        $data['is_active'] = $request->boolean('is_active', $country?->is_active ?? false);
        $data['tax_needs_review'] = $request->boolean('tax_needs_review');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['tax_percent'] = $data['tax_percent'] ?? null;
        $localesCsv = (string) ($data['locales_csv'] ?? '');
        unset($data['locales_csv']);
        $defaultLocale = $data['default_locale'] ?? null;
        $data['locales'] = $localesCsv !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $localesCsv))))
            : ($defaultLocale ? [$defaultLocale] : []);

        return $data;
    }

    private function assertReadyToActivate(Country $country): void
    {
        $readiness = CountryReadiness::check($country);
        if ($readiness['ready']) {
            return;
        }

        throw ValidationException::withMessages([
            'is_active' => 'Cannot activate until ready: '.implode(', ', $readiness['missing']),
        ]);
    }

    private function assertCountryAccess(?User $user, Country $country): void
    {
        if (! $user || ! $user->isCountryScoped()) {
            return;
        }

        abort_unless((int) $user->country_id === (int) $country->id, 403);
    }
}
