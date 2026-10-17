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
            ['group' => 'site', 'key' => 'site.email', 'value' => 'hello@luxsecure.example', 'type' => 'string', 'label' => 'Contact email'],
            ['group' => 'site', 'key' => 'site.phone', 'value' => '+92 300 0000000', 'type' => 'string', 'label' => 'Contact phone'],
            ['group' => 'commerce', 'key' => 'commerce.commission_rate', 'value' => '2.5', 'type' => 'number', 'label' => 'Commission rate (%)'],
            ['group' => 'commerce', 'key' => 'commerce.tax_rate', 'value' => '0', 'type' => 'number', 'label' => 'Tax rate (%)'],
            ['group' => 'policies', 'key' => 'policies.privacy', 'value' => 'Privacy policy placeholder.', 'type' => 'text', 'label' => 'Privacy policy'],
            ['group' => 'policies', 'key' => 'policies.terms', 'value' => 'Terms of service placeholder.', 'type' => 'text', 'label' => 'Terms of service'],
            ['group' => 'policies', 'key' => 'policies.cancellation', 'value' => 'Flexible cancellation by default.', 'type' => 'text', 'label' => 'Cancellation policy'],
        ];

        foreach ($defaults as $row) {
            Setting::query()->firstOrCreate(['key' => $row['key']], $row);
        }
    }
}
