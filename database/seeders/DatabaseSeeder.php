<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ReferenceDataSeeder::class,
        ]);

        // Demo users, listings, hotel inventory, CMS drafts — never in production.
        if (app()->environment(['local', 'testing'])) {
            $this->call([
                AdminAndPropertiesSeeder::class,
                SettingsSeeder::class,
                CmsPagesSeeder::class,
                DemoOrganizationSeeder::class,
            ]);
        }
    }
}
