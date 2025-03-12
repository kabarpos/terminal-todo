<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // Cari atau buat super admin user
            $superAdmin = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'status' => 'active',
                    'approved_at' => now(),
                ]
            );

            // Get or create the Super Admin role
            $superAdminRole = Role::firstOrCreate([
                'name' => 'Super Admin',
                'guard_name' => 'web'
            ]);

            // Get all permissions
            $permissions = Permission::all();

            // Assign all permissions to Super Admin role
            $superAdminRole->syncPermissions($permissions);

            // Assign role to super admin
            $superAdmin->syncRoles([$superAdminRole]);

            // Log success
            \Log::info('Super Admin updated successfully with all permissions');
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating Super Admin: ' . $e->getMessage());
            throw $e;
        }
    }
}