<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Kept for tests that call SettingsSeeder directly.
 * Brand + commerce defaults live in ReferenceDataSeeder (all environments).
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ReferenceDataSeeder::class);
    }
}
