<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            SuperAdminSeeder::class,
            SettingsSeeder::class,
            CategorySeeder::class,
            PlatformSeeder::class,
            TaskSeeder::class,
            SocialPlatformSeeder::class,
            MetricSeeder::class,
            SocialAccountSeeder::class,
            MetricDataSeeder::class
        ]);
    }
}