<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class, // Jalankan ini dulu untuk membuat role dan permission
            SuperAdminSeeder::class, // Kemudian buat super admin dengan role dan permission
            SettingsSeeder::class,
            CategorySeeder::class,
            PlatformSeeder::class,
            TaskSeeder::class,
        ]);
    }
}