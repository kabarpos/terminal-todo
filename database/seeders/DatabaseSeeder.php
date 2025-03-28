<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // 1. Urutan pemanggilan seeders sangat penting
            // 2. Seeder yang menyiapkan data dasar (master data) harus dijalankan lebih dulu
            // 3. Seeder yang bergantung pada data dari seeder lain harus dijalankan kemudian
            
            Log::info('Starting database seeding process');
            
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // 1. Role and Permission (harus pertama karena dibutuhkan untuk user)
            $this->call(RoleAndPermissionSeeder::class);
            
            // 2. User Admin
            $this->call(SuperAdminSeeder::class);
            
            // 3. Settings
            $this->call(SettingsSeeder::class);
            
            // 4. Data Master (kategori, platform)
            $this->call(CategorySeeder::class);
            $this->call(PlatformSeeder::class);
            
            // 5. Data transaksional (task)
            $this->call(TaskSeeder::class);
            
            // 6. Data metrics dan metrics data
            $this->call(MetricSeeder::class);
            $this->call(SocialAccountSeeder::class);
            $this->call(MetricDataSeeder::class);
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            Log::info('Database seeding completed successfully');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            Log::error('Error seeding database: ' . $e->getMessage());
            // Rethrow after logging
            throw $e;
        }
    }
}