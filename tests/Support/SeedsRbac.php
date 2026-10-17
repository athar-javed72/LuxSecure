<?php

namespace Tests\Support;

use Database\Seeders\RolesAndPermissionsSeeder;

trait SeedsRbac
{
    protected function seedRbac(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
    }
}
