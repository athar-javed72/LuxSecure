<?php

namespace App\Support;

use App\Models\CancellationPolicy;
use App\Models\Country;
use App\Models\CountryPaymentMethod;
use App\Models\KycDocumentType;
use App\Models\Page;

class CountryReadiness
{
    /**
     * @return array{
     *     ready: bool,
     *     missing: list<string>,
     *     legal: array<string, array{ok: bool, source: string|null, title: string|null, slug: string|null, id: int|null}>
     * }
     */
    public static function check(Country $country): array
    {
        $missing = [];

        if (! filled($country->currency_code)) {
            $missing[] = 'Currency code';
        }
        if (! filled($country->timezone)) {
            $missing[] = 'Timezone';
        }
        if (! filled($country->default_locale)) {
            $missing[] = 'Default locale';
        }

        $taxOk = filled($country->tax_name)
            || (float) $country->tax_percent > 0
            || CountrySettingValue::get($country->id, 'commerce.no_tax') === '1'
            || CountrySettingValue::get($country->id, 'commerce.tax_exempt') === '1';
        if (! $taxOk) {
            $missing[] = 'Tax configuration (or mark no tax)';
        }

        $hasKyc = KycDocumentType::query()
            ->where(function ($q) use ($country) {
                $q->where('country_id', $country->id)->orWhereNull('country_id');
            })
            ->exists();
        if (! $hasKyc) {
            $missing[] = 'At least one KYC document type';
        }

        $hasPayment = CountryPaymentMethod::query()
            ->where('country_id', $country->id)
            ->where('is_enabled', true)
            ->exists();
        if (! $hasPayment) {
            $missing[] = 'At least one enabled payment method';
        }

        $hasPolicy = CancellationPolicy::query()
            ->where(function ($q) use ($country) {
                $q->where('country_id', $country->id)->orWhereNull('country_id');
            })
            ->where('is_active', true)
            ->exists();
        if (! $hasPolicy) {
            $missing[] = 'Default cancellation policy';
        }

        $legal = [];
        foreach (['privacy', 'terms'] as $type) {
            $page = Page::resolveLegal($type, $country->id);
            if (! $page) {
                $missing[] = 'Published '.$type.' page (country or global fallback)';
                $legal[$type] = [
                    'ok' => false,
                    'source' => null,
                    'title' => null,
                    'slug' => null,
                    'id' => null,
                ];

                continue;
            }

            $source = $page->country_id === null
                ? 'global'
                : ((int) $page->country_id === (int) $country->id ? 'country' : 'other');

            $legal[$type] = [
                'ok' => true,
                'source' => $source,
                'title' => $page->title,
                'slug' => $page->slug,
                'id' => $page->id,
            ];
        }

        return [
            'ready' => $missing === [],
            'missing' => $missing,
            'legal' => $legal,
        ];
    }
}
