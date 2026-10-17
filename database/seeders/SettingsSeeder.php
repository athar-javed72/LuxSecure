<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['group' => 'site', 'key' => 'site.name', 'value' => 'LuxSecure', 'type' => 'string', 'label' => 'Site name'],
            ['group' => 'site', 'key' => 'site.tagline', 'value' => 'Verified luxury property marketplace', 'type' => 'string', 'label' => 'Tagline'],
            ['group' => 'site', 'key' => 'site.email', 'value' => '[PLACEHOLDER: CONTACT_EMAIL]', 'type' => 'string', 'label' => 'Contact email'],
            ['group' => 'site', 'key' => 'site.phone', 'value' => '[PLACEHOLDER: CONTACT_PHONE]', 'type' => 'string', 'label' => 'Contact phone'],
            ['group' => 'site', 'key' => 'site.hours', 'value' => '[PLACEHOLDER: BUSINESS_HOURS]', 'type' => 'string', 'label' => 'Business hours'],
            ['group' => 'commerce', 'key' => 'commerce.commission_rate', 'value' => '2.5', 'type' => 'number', 'label' => 'Commission rate (%)'],
            ['group' => 'commerce', 'key' => 'commerce.tax_rate', 'value' => '0', 'type' => 'number', 'label' => 'Tax rate (%)'],
            ['group' => 'policies', 'key' => 'policies.privacy', 'value' => 'See CMS draft page: /pages/privacy-policy (publish after legal review).', 'type' => 'text', 'label' => 'Privacy policy'],
            ['group' => 'policies', 'key' => 'policies.terms', 'value' => 'See CMS draft page: /pages/terms-of-service (publish after legal review).', 'type' => 'text', 'label' => 'Terms of service'],
            ['group' => 'policies', 'key' => 'policies.cancellation', 'value' => 'See CMS draft page: /pages/refund-and-cancellation-policy (publish after legal review).', 'type' => 'text', 'label' => 'Cancellation policy'],
        ];

        foreach ($defaults as $row) {
            Setting::query()->firstOrCreate(['key' => $row['key']], $row);
        }
    }
}
