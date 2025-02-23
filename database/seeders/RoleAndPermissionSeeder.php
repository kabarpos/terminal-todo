<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Truncate existing records to start fresh
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Task Management
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'assign tasks',
            'change task status',
            
            // Content Management
            'view content',
            'create content',
            'edit content',
            'delete content',
            'approve content',
            
            // Team Management
            'view teams',
            'create teams',
            'edit teams',
            'delete teams',
            'manage team members',
            
            // Calendar Management
            'view calendar',
            'create calendar events',
            'edit calendar events',
            'delete calendar events',
            
            // Asset Management
            'view assets',
            'upload assets',
            'edit assets',
            'delete assets',
            
            // Report Access
            'view reports',
            'export reports',
            'view analytics',

            // Additional permissions for super admin
            'manage all',
            'access admin',
            'manage settings'
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'super_admin' => [
                'name' => 'Super Admin',
                'permissions' => Permission::all() // Get all permissions
            ],
            'content_manager' => [
                'name' => 'Content Manager',
                'permissions' => [
                    'view tasks', 'create tasks', 'edit tasks', 'assign tasks', 'change task status',
                    'view content', 'create content', 'edit content', 'approve content',
                    'view calendar', 'create calendar events', 'edit calendar events',
                    'view assets', 'upload assets', 'edit assets',
                    'view reports', 'view analytics'
                ]
            ],
            'content_writer' => [
                'name' => 'Content Writer',
                'permissions' => [
                    'view tasks', 'edit tasks', 'change task status',
                    'view content', 'create content', 'edit content',
                    'view calendar',
                    'view assets', 'upload assets'
                ]
            ],
            'designer' => [
                'name' => 'Designer',
                'permissions' => [
                    'view tasks', 'edit tasks', 'change task status',
                    'view content', 'create content', 'edit content',
                    'view calendar',
                    'view assets', 'upload assets', 'edit assets'
                ]
            ],
            'video_editor' => [
                'name' => 'Video Editor',
                'permissions' => [
                    'view tasks', 'edit tasks', 'change task status',
                    'view content', 'create content', 'edit content',
                    'view calendar',
                    'view assets', 'upload assets', 'edit assets'
                ]
            ],
            'social_media' => [
                'name' => 'Social Media',
                'permissions' => [
                    'view tasks', 'edit tasks', 'change task status',
                    'view content', 'create content',
                    'view calendar', 'create calendar events',
                    'view assets', 'upload assets'
                ]
            ]
        ];

        foreach ($roles as $roleKey => $roleData) {
            $role = Role::create(['name' => $roleData['name']]);
            if ($roleKey === 'super_admin') {
                $role->givePermissionTo(Permission::all());
            } else {
                $permissionNames = $roleData['permissions'];
                $permissions = Permission::whereIn('name', $permissionNames)->get();
                $role->givePermissionTo($permissions);
            }
        }
    }
}