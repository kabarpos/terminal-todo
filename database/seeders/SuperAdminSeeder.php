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
            // Hapus data yang terkait dengan user admin
            DB::table('tasks')->where('created_by', function($query) {
                $query->select('id')
                    ->from('users')
                    ->where('email', 'admin@example.com');
            })->delete();

            // Hapus user admin yang mungkin sudah ada
            User::where('email', 'admin@example.com')->delete();

            // Create super admin user
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => 'active',
                'approved_at' => now(),
            ]);

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
            $superAdmin->assignRole($superAdminRole);

            // Log success
            \Log::info('Super Admin created successfully with all permissions');
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating Super Admin: ' . $e->getMessage());
            throw $e;
        }
    }
}