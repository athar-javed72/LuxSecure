<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\CancellationPolicy;
use App\Models\CancellationPolicyRule;
use App\Models\City;
use App\Models\Country;
use App\Models\CountryPaymentMethod;
use App\Models\CountrySetting;
use App\Models\Currency;
use App\Models\KycDocumentType;
use App\Models\Language;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Idempotent reference data for all environments.
 * Uses firstOrCreate so Admin edits are never overwritten.
 */
class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBrandAndGlobalSettings();
        $this->seedCurrencies();
        $this->seedLanguages();
        $countries = $this->seedCountries();
        $this->seedCountryContactAndCommerce($countries);
        $this->seedCities($countries);
        $this->seedAreas($countries);
        $this->seedKycTypes($countries);
        $this->seedPaymentMethods($countries);
        $this->seedCancellationPolicies();
    }

    private function seedBrandAndGlobalSettings(): void
    {
        $hoursJson = json_encode([
            'is_24_7' => true,
            'label' => '24 hours',
            'days' => [],
        ], JSON_UNESCAPED_UNICODE);

        $defaults = [
            ['group' => 'site', 'key' => 'site.name', 'value' => 'Maskanoor', 'type' => 'string', 'label' => 'Site name'],
            ['group' => 'site', 'key' => 'site.legal_name', 'value' => 'Maskanoor', 'type' => 'string', 'label' => 'Legal name'],
            ['group' => 'site', 'key' => 'site.tagline', 'value' => 'Elevating Spaces. Enriching Stays.', 'type' => 'string', 'label' => 'Tagline (fallback)'],
            ['group' => 'site', 'key' => 'site.tagline.en', 'value' => 'Elevating Spaces. Enriching Stays.', 'type' => 'string', 'label' => 'Tagline (en)'],
            ['group' => 'site', 'key' => 'site.tagline.ur', 'value' => 'اپنا ٹھکانہ، بھروسے کے ساتھ', 'type' => 'string', 'label' => 'Tagline (ur)'],
            ['group' => 'site', 'key' => 'site.tagline.ar', 'value' => 'مسكنك، بثقة', 'type' => 'string', 'label' => 'Tagline (ar)'],
            ['group' => 'site', 'key' => 'site.tagline.ms', 'value' => 'Cari tempat anda, dengan kepercayaan.', 'type' => 'string', 'label' => 'Tagline (ms)'],
            ['group' => 'site', 'key' => 'site.logo', 'value' => '/brand/logos/maskanoor-group-horizontal-for-light-bg.svg', 'type' => 'string', 'label' => 'Logo path'],
            ['group' => 'site', 'key' => 'site.favicon', 'value' => '/brand/favicon/favicon.svg', 'type' => 'string', 'label' => 'Favicon path'],
            ['group' => 'site', 'key' => 'site.email', 'value' => 'pmobie264@gmail.com', 'type' => 'string', 'label' => 'Contact email'],
            ['group' => 'site', 'key' => 'site.phone', 'value' => '+923228439536', 'type' => 'string', 'label' => 'Contact phone'],
            ['group' => 'site', 'key' => 'site.hours', 'value' => '24 hours', 'type' => 'string', 'label' => 'Business hours label'],
            ['group' => 'site', 'key' => 'site.hours_structured', 'value' => $hoursJson, 'type' => 'json', 'label' => 'Business hours (structured)'],
            ['group' => 'commerce', 'key' => 'commerce.commission_hotel', 'value' => '10', 'type' => 'number', 'label' => 'Hotel commission (%)'],
            ['group' => 'commerce', 'key' => 'commerce.commission_rent', 'value' => '5', 'type' => 'number', 'label' => 'Rent commission (%)'],
            ['group' => 'commerce', 'key' => 'commerce.commission_sale', 'value' => '1', 'type' => 'number', 'label' => 'Sale commission (%)'],
            ['group' => 'commerce', 'key' => 'commerce.commission_rate', 'value' => '5', 'type' => 'number', 'label' => 'Default commission (%)'],
            ['group' => 'commerce', 'key' => 'commerce.tax_rate', 'value' => '0', 'type' => 'number', 'label' => 'Default tax rate (%)'],
            ['group' => 'policies', 'key' => 'policies.privacy', 'value' => 'See CMS draft page: /pages/privacy-policy (publish after legal review).', 'type' => 'text', 'label' => 'Privacy policy'],
            ['group' => 'policies', 'key' => 'policies.terms', 'value' => 'See CMS draft page: /pages/terms-of-service (publish after legal review).', 'type' => 'text', 'label' => 'Terms of service'],
            ['group' => 'policies', 'key' => 'policies.cancellation', 'value' => 'See CMS draft page: /pages/refund-and-cancellation-policy (publish after legal review).', 'type' => 'text', 'label' => 'Cancellation policy'],
        ];

        foreach ($defaults as $row) {
            // Brand asset paths / tagline should refresh when the logo kit changes.
            if (in_array($row['key'], [
                'site.logo',
                'site.favicon',
                'site.tagline',
                'site.tagline.en',
            ], true)) {
                Setting::query()->updateOrCreate(['key' => $row['key']], $row);
            } else {
                Setting::query()->firstOrCreate(['key' => $row['key']], $row);
            }
        }
    }

    private function seedCurrencies(): void
    {
        foreach ([
            ['code' => 'PKR', 'symbol' => 'Rs', 'decimals' => 0, 'rate_to_base' => 1],
            ['code' => 'SAR', 'symbol' => '﷼', 'decimals' => 2, 'rate_to_base' => null],
            ['code' => 'AED', 'symbol' => 'د.إ', 'decimals' => 2, 'rate_to_base' => null],
            ['code' => 'QAR', 'symbol' => '﷼', 'decimals' => 2, 'rate_to_base' => null],
            ['code' => 'MYR', 'symbol' => 'RM', 'decimals' => 2, 'rate_to_base' => null],
            ['code' => 'GBP', 'symbol' => '£', 'decimals' => 2, 'rate_to_base' => null],
        ] as $row) {
            Currency::query()->firstOrCreate(
                ['code' => $row['code']],
                array_merge($row, ['is_active' => true])
            );
        }
    }

    private function seedLanguages(): void
    {
        foreach ([
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'is_rtl' => false, 'needs_native_review' => false, 'sort_order' => 1],
            ['code' => 'ur', 'name' => 'Urdu', 'native_name' => 'اردو', 'is_rtl' => true, 'needs_native_review' => false, 'sort_order' => 2],
            ['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'is_rtl' => true, 'needs_native_review' => true, 'sort_order' => 3],
            ['code' => 'ms', 'name' => 'Malay', 'native_name' => 'Bahasa Melayu', 'is_rtl' => false, 'needs_native_review' => true, 'sort_order' => 4],
        ] as $row) {
            Language::query()->firstOrCreate(
                ['code' => $row['code']],
                array_merge($row, ['is_active' => true])
            );
        }
    }

    /**
     * @return array<string, Country>
     */
    private function seedCountries(): array
    {
        $defs = [
            'PK' => [
                'name' => 'Pakistan',
                'currency_code' => 'PKR',
                'default_locale' => 'en',
                'locales' => ['ur', 'en'],
                'timezone' => 'Asia/Karachi',
                'phone_code' => '+92',
                'phone_regex' => '^\+?92[0-9]{10}$|^0?3[0-9]{9}$',
                'area_units' => ['marla', 'kanal', 'sq_ft'],
                'tax_name' => 'Sales Tax',
                'tax_percent' => 0,
                'tax_needs_review' => true,
                'invoice_prefix' => 'PK',
                'sort_order' => 1,
            ],
            'SA' => [
                'name' => 'Saudi Arabia',
                'currency_code' => 'SAR',
                'default_locale' => 'ar',
                'locales' => ['ar', 'en'],
                'timezone' => 'Asia/Riyadh',
                'phone_code' => '+966',
                'phone_regex' => '^\+?966[0-9]{8,10}$',
                'area_units' => ['sq_m'],
                'tax_name' => 'VAT',
                'tax_percent' => 15,
                'tax_needs_review' => false,
                'invoice_prefix' => 'SA',
                'sort_order' => 2,
            ],
            'AE' => [
                'name' => 'United Arab Emirates',
                'currency_code' => 'AED',
                'default_locale' => 'en',
                'locales' => ['en', 'ar'],
                'timezone' => 'Asia/Dubai',
                'phone_code' => '+971',
                'phone_regex' => '^\+?971[0-9]{8,9}$',
                'area_units' => ['sq_ft'],
                'tax_name' => 'VAT',
                'tax_percent' => 5,
                'tax_needs_review' => false,
                'invoice_prefix' => 'AE',
                'sort_order' => 3,
            ],
            'QA' => [
                'name' => 'Qatar',
                'currency_code' => 'QAR',
                'default_locale' => 'ar',
                'locales' => ['ar', 'en'],
                'timezone' => 'Asia/Qatar',
                'phone_code' => '+974',
                'phone_regex' => '^\+?974[0-9]{8}$',
                'area_units' => ['sq_m'],
                'tax_name' => 'Tax',
                'tax_percent' => 0,
                'tax_needs_review' => false,
                'invoice_prefix' => 'QA',
                'sort_order' => 4,
            ],
            'MY' => [
                'name' => 'Malaysia',
                'currency_code' => 'MYR',
                'default_locale' => 'en',
                'locales' => ['en', 'ms'],
                'timezone' => 'Asia/Kuala_Lumpur',
                'phone_code' => '+60',
                'phone_regex' => '^\+?60[0-9]{8,11}$',
                'area_units' => ['sq_ft'],
                'tax_name' => 'SST',
                'tax_percent' => 8,
                'tax_needs_review' => false,
                'invoice_prefix' => 'MY',
                'sort_order' => 5,
            ],
            'GB' => [
                'name' => 'United Kingdom',
                'currency_code' => 'GBP',
                'default_locale' => 'en',
                'locales' => ['en'],
                'timezone' => 'Europe/London',
                'phone_code' => '+44',
                'phone_regex' => '^\+?44[0-9]{9,11}$',
                'area_units' => ['sq_ft'],
                'tax_name' => 'VAT',
                'tax_percent' => 20,
                'tax_needs_review' => false,
                'invoice_prefix' => 'GB',
                'sort_order' => 6,
            ],
        ];

        $out = [];
        foreach ($defs as $iso2 => $data) {
            $country = Country::query()->firstOrCreate(
                ['iso2' => $iso2],
                array_merge($data, ['is_active' => true, 'invoice_sequence' => 0])
            );

            // Fill locales if missing; never overwrite tax fields edited in Admin.
            if (blank($country->locales) && Schema::hasColumn('countries', 'locales')) {
                $country->update(['locales' => $data['locales']]);
                $country->refresh();
            }

            $out[$iso2] = $country;
        }

        return $out;
    }

    /**
     * @param  array<string, Country>  $countries
     */
    private function seedCountryContactAndCommerce(array $countries): void
    {
        $pk = $countries['PK'];
        $hoursJson = Setting::getValue('site.hours_structured');

        foreach ([
            ['key' => 'site.email', 'value' => 'pmobie264@gmail.com', 'type' => 'string', 'label' => 'Email'],
            ['key' => 'site.phone', 'value' => '+923228439536', 'type' => 'string', 'label' => 'Phone'],
            ['key' => 'site.hours', 'value' => '24 hours', 'type' => 'string', 'label' => 'Hours'],
            ['key' => 'site.hours_structured', 'value' => $hoursJson, 'type' => 'json', 'label' => 'Hours structured'],
            ['key' => 'commerce.commission_hotel', 'value' => '10', 'type' => 'number', 'label' => 'Hotel commission (%)'],
            ['key' => 'commerce.commission_rent', 'value' => '5', 'type' => 'number', 'label' => 'Rent commission (%)'],
            ['key' => 'commerce.commission_sale', 'value' => '1', 'type' => 'number', 'label' => 'Sale commission (%)'],
        ] as $row) {
            CountrySetting::query()->firstOrCreate(
                ['country_id' => $pk->id, 'key' => $row['key']],
                [
                    'value' => $row['value'],
                    'type' => $row['type'],
                    'label' => $row['label'],
                ]
            );
        }

        // Commerce defaults for other countries (no fake contact numbers).
        foreach ($countries as $iso2 => $country) {
            if ($iso2 === 'PK') {
                continue;
            }
            foreach ([
                ['key' => 'commerce.commission_hotel', 'value' => '10', 'label' => 'Hotel commission (%)'],
                ['key' => 'commerce.commission_rent', 'value' => '5', 'label' => 'Rent commission (%)'],
                ['key' => 'commerce.commission_sale', 'value' => '1', 'label' => 'Sale commission (%)'],
            ] as $row) {
                CountrySetting::query()->firstOrCreate(
                    ['country_id' => $country->id, 'key' => $row['key']],
                    [
                        'value' => $row['value'],
                        'type' => 'number',
                        'label' => $row['label'],
                    ]
                );
            }
        }
    }

    /**
     * @param  array<string, Country>  $countries
     */
    private function seedCities(array $countries): void
    {
        $map = [
            'PK' => ['Islamabad', 'Lahore', 'Karachi'],
            'SA' => ['Riyadh', 'Jeddah', 'Makkah', 'Madinah'],
            'AE' => ['Dubai', 'Abu Dhabi'],
            'QA' => ['Doha'],
            'MY' => ['Kuala Lumpur', 'Penang'],
            'GB' => ['London', 'Manchester'],
        ];

        foreach ($map as $iso2 => $names) {
            $country = $countries[$iso2];
            foreach ($names as $name) {
                City::query()->firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'slug' => Str::slug($name),
                    ],
                    [
                        'name' => $name,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * @param  array<string, Country>  $countries
     */
    private function seedAreas(array $countries): void
    {
        $map = [
            'PK' => [
                'Islamabad' => ['F-6', 'F-7', 'Blue Area'],
                'Lahore' => ['DHA', 'Gulberg'],
                'Karachi' => ['Clifton', 'PECHS'],
            ],
            'SA' => [
                'Riyadh' => ['Olaya', 'Diplomatic Quarter'],
                'Jeddah' => ['Corniche'],
            ],
            'AE' => [
                'Dubai' => ['Downtown', 'Marina'],
                'Abu Dhabi' => ['Corniche'],
            ],
            'QA' => [
                'Doha' => ['West Bay', 'The Pearl'],
            ],
            'MY' => [
                'Kuala Lumpur' => ['KLCC', 'Bukit Bintang'],
                'Penang' => ['Georgetown'],
            ],
            'GB' => [
                'London' => ['Westminster', 'Canary Wharf'],
                'Manchester' => ['City Centre'],
            ],
        ];

        foreach ($map as $iso2 => $cities) {
            $country = $countries[$iso2];
            foreach ($cities as $cityName => $areas) {
                $city = City::query()
                    ->where('country_id', $country->id)
                    ->where('slug', Str::slug($cityName))
                    ->first();
                if (! $city) {
                    continue;
                }
                foreach ($areas as $areaName) {
                    Area::query()->firstOrCreate(
                        [
                            'city_id' => $city->id,
                            'slug' => Str::slug($areaName),
                        ],
                        [
                            'country_id' => $country->id,
                            'name' => $areaName,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }

    /**
     * @param  array<string, Country>  $countries
     */
    private function seedKycTypes(array $countries): void
    {
        $defs = [
            'PK' => [
                ['name' => 'CNIC', 'code' => 'cnic', 'is_required' => true, 'for_roles' => ['agent', 'landlord', 'hotel_manager', 'client']],
                ['name' => 'Ownership registry', 'code' => 'registry', 'is_required' => true, 'for_roles' => ['landlord', 'hotel_manager']],
                ['name' => 'Trade / agent license', 'code' => 'license', 'is_required' => true, 'for_roles' => ['agent']],
            ],
            'SA' => [
                ['name' => 'National ID', 'code' => 'national_id', 'is_required' => true, 'for_roles' => ['agent', 'landlord', 'hotel_manager', 'client']],
                ['name' => 'Iqama', 'code' => 'iqama', 'is_required' => false, 'for_roles' => ['agent', 'landlord', 'hotel_manager', 'client']],
            ],
            'AE' => [
                ['name' => 'Emirates ID', 'code' => 'emirates_id', 'is_required' => true, 'for_roles' => ['agent', 'landlord', 'hotel_manager', 'client']],
            ],
            'QA' => [
                ['name' => 'QID', 'code' => 'qid', 'is_required' => true, 'for_roles' => ['agent', 'landlord', 'hotel_manager', 'client']],
            ],
            'MY' => [
                ['name' => 'MyKad', 'code' => 'mykad', 'is_required' => true, 'for_roles' => ['agent', 'landlord', 'hotel_manager', 'client']],
                ['name' => 'Passport', 'code' => 'passport', 'is_required' => false, 'for_roles' => ['agent', 'landlord', 'hotel_manager', 'client']],
            ],
            'GB' => [
                ['name' => 'Passport', 'code' => 'passport', 'is_required' => true, 'for_roles' => ['agent', 'landlord', 'hotel_manager', 'client']],
                ['name' => 'Driving licence', 'code' => 'driving_licence', 'is_required' => false, 'for_roles' => ['agent', 'landlord', 'hotel_manager', 'client']],
                ['name' => 'Proof of address', 'code' => 'proof_of_address', 'is_required' => true, 'for_roles' => ['agent', 'landlord', 'hotel_manager']],
            ],
        ];

        foreach ($defs as $iso2 => $types) {
            $country = $countries[$iso2];
            foreach ($types as $i => $type) {
                KycDocumentType::query()->firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'code' => $type['code'],
                    ],
                    [
                        'name' => $type['name'],
                        'is_required' => $type['is_required'],
                        'for_roles' => $type['for_roles'],
                        'sort_order' => $i + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * @param  array<string, Country>  $countries
     */
    private function seedPaymentMethods(array $countries): void
    {
        foreach ($countries as $iso2 => $country) {
            $gateways = [
                ['gateway' => 'bank_transfer', 'is_enabled' => true],
                ['gateway' => 'card', 'is_enabled' => false],
            ];
            if ($iso2 === 'PK') {
                $gateways[] = ['gateway' => 'jazzcash', 'is_enabled' => false];
                $gateways[] = ['gateway' => 'easypaisa', 'is_enabled' => false];
            }

            foreach ($gateways as $method) {
                CountryPaymentMethod::query()->firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'gateway' => $method['gateway'],
                    ],
                    [
                        'is_enabled' => $method['is_enabled'],
                        'meta' => null,
                    ]
                );
            }
        }
    }

    private function seedCancellationPolicies(): void
    {
        $policies = [
            [
                'slug' => 'hotel-flexible',
                'name' => 'Hotel Flexible',
                'applies_to' => 'hotel',
                'reference_date' => 'check_in',
                'is_default' => true,
                'rules' => [
                    ['days_before_min' => 7, 'refund_percent' => 100],
                    ['days_before_min' => 3, 'refund_percent' => 50],
                    ['days_before_min' => 0, 'refund_percent' => 0],
                ],
            ],
            [
                'slug' => 'hotel-strict',
                'name' => 'Hotel Strict',
                'applies_to' => 'hotel',
                'reference_date' => 'check_in',
                'is_default' => false,
                'rules' => [
                    ['days_before_min' => 14, 'refund_percent' => 100],
                    ['days_before_min' => 7, 'refund_percent' => 50],
                    ['days_before_min' => 0, 'refund_percent' => 0],
                ],
            ],
            [
                'slug' => 'hotel-non-refundable',
                'name' => 'Hotel Non-refundable',
                'applies_to' => 'hotel',
                'reference_date' => 'check_in',
                'is_default' => false,
                'rules' => [
                    ['days_before_min' => 0, 'refund_percent' => 0],
                ],
            ],
            [
                'slug' => 'rent-standard',
                'name' => 'Rent Standard',
                'applies_to' => 'rent',
                'reference_date' => 'move_in',
                'is_default' => true,
                'rules' => [
                    ['days_before_min' => 14, 'refund_percent' => 100],
                    ['days_before_min' => 7, 'refund_percent' => 50],
                    ['days_before_min' => 0, 'refund_percent' => 0],
                ],
            ],
            [
                'slug' => 'sale-token',
                'name' => 'Sale Token',
                'applies_to' => 'sale',
                'reference_date' => 'agreement_date',
                'is_default' => true,
                'rules' => [
                    ['days_before_min' => 7, 'refund_percent' => 100],
                    ['days_before_min' => 0, 'refund_percent' => 0],
                ],
            ],
            // Legacy alias kept for existing bookings/tests; do not overwrite rules if already present.
            [
                'slug' => 'flexible',
                'name' => 'Flexible',
                'applies_to' => 'hotel',
                'reference_date' => 'check_in',
                'is_default' => false,
                'rules' => [
                    ['days_before_min' => 7, 'refund_percent' => 100],
                    ['days_before_min' => 3, 'refund_percent' => 50],
                    ['days_before_min' => 0, 'refund_percent' => 0],
                ],
            ],
        ];

        foreach ($policies as $def) {
            $policy = CancellationPolicy::query()->firstOrCreate(
                [
                    'slug' => $def['slug'],
                    'country_id' => null,
                    'organization_id' => null,
                ],
                [
                    'name' => $def['name'],
                    'applies_to' => $def['applies_to'],
                    'reference_date' => $def['reference_date'],
                    'is_default' => $def['is_default'],
                    'is_active' => true,
                    'description' => 'Global default '.$def['name'].' policy.',
                ]
            );

            if ($policy->wasRecentlyCreated) {
                foreach ($def['rules'] as $order => $rule) {
                    CancellationPolicyRule::query()->create([
                        'cancellation_policy_id' => $policy->id,
                        'days_before_min' => $rule['days_before_min'],
                        'refund_percent' => $rule['refund_percent'],
                        'fee_fixed' => null,
                        'sort_order' => $order,
                    ]);
                }
            } else {
                // Fill new columns if missing without touching Admin-edited rules.
                $updates = [];
                if (blank($policy->applies_to)) {
                    $updates['applies_to'] = $def['applies_to'];
                }
                if (blank($policy->reference_date)) {
                    $updates['reference_date'] = $def['reference_date'];
                }
                if ($updates !== []) {
                    $policy->update($updates);
                }
            }
        }

        // Demote legacy migration "flexible" default when purpose-specific defaults exist (idempotent).
        foreach (['hotel-flexible', 'rent-standard', 'sale-token'] as $slug) {
            $policy = CancellationPolicy::query()
                ->whereNull('country_id')
                ->whereNull('organization_id')
                ->where('slug', $slug)
                ->first();
            if ($policy && ! $policy->is_default) {
                $policy->update(['is_default' => true]);
            }
        }

        CancellationPolicy::query()
            ->whereNull('country_id')
            ->whereNull('organization_id')
            ->where('slug', 'flexible')
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }
}
