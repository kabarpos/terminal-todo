<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
            'approved_at' => now(),
        ]);

        // Get the Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if (!$superAdminRole) {
            // If role doesn't exist, create it with all permissions
            $superAdminRole = Role::create(['name' => 'Super Admin']);
            $superAdminRole->givePermissionTo('*');
        }

        // Assign role to super admin
        $superAdmin->assignRole($superAdminRole);

        // Sync all permissions directly to the user as well
        $superAdmin->syncPermissions($superAdminRole->permissions);
    }
}