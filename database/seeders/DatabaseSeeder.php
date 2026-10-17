<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Demo users, listings, hotel inventory, and site settings — never in production.
        if (app()->environment(['local', 'testing'])) {
            $this->call([
                AdminAndPropertiesSeeder::class,
                SettingsSeeder::class,
                CmsPagesSeeder::class,
            ]);
        }
    }
}
