<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // Cari atau buat admin user
            $admin = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Admin',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'status' => 'active',
                    'approved_at' => now(),
                ]
            );

            // Get or create the Admin role
            $adminRole = Role::firstOrCreate([
                'name' => 'Admin',
                'guard_name' => 'web'
            ]);

            // Get all permissions
            $permissions = Permission::all();

            if ($permissions->isEmpty()) {
                Log::warning('No permissions found when running SuperAdminSeeder. Make sure to run RoleAndPermissionSeeder first.');
            }

            // Assign all permissions to Admin role
            $adminRole->syncPermissions($permissions);

            // Assign role to admin
            $admin->syncRoles([$adminRole]);

            // Log success
            Log::info('Admin user updated successfully with all permissions');
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating Admin user: ' . $e->getMessage());
            throw $e;
        }
    }
}