<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            NewPermissionSeeder::class,
            SuperAdminSeeder::class,
            SettingsSeeder::class,
            CategorySeeder::class,
            PlatformSeeder::class,
            TaskSeeder::class,
        ]);
    }
}