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

        try {
            // Create permissions
            $permissions = [
                // Dashboard
                'view dashboard',
                
                // Calendar
                'view calendar',
                'create calendar',
                'edit calendar',
                'delete calendar',
                
                // Tasks
                'view task',
                'create task',
                'edit task',
                'delete task',
                
                // Teams
                'view team',
                'create team',
                'edit team',
                'delete team',
                
                // Categories
                'view category',
                'create category',
                'edit category',
                'delete category',
                
                // Platforms
                'view platform',
                'create platform',
                'edit platform',
                'delete platform',
                
                // News Feed
                'view newsfeed',
                'create newsfeed',
                'edit newsfeed',
                'delete newsfeed',
                
                // Social Media Reports
                'view social media report',
                'create social media report',
                'edit social media report',
                'delete social media report',
                
                // Assets
                'view asset',
                'create asset',
                'delete asset',
                
                // Analytics
                'view analytics',
                
                // Admin permissions
                'view users',
                'create users',
                'edit users',
                'delete users',
                'view roles',
                'create roles',
                'edit roles',
                'delete roles',
                'manage settings'
            ];

            // Create all permissions
            $createdPermissions = [];
            foreach ($permissions as $permission) {
                $createdPermissions[] = Permission::create(['name' => $permission, 'guard_name' => 'web']);
            }

            // Create roles and assign permissions
            $roles = [
                'super_admin' => [
                    'name' => 'Super Admin',
                    'permissions' => $permissions // All permissions
                ],
                'content_manager' => [
                    'name' => 'Content Manager',
                    'permissions' => [
                        'view dashboard',
                        'view calendar', 'create calendar', 'edit calendar', 'delete calendar',
                        'view task', 'create task', 'edit task', 'delete task',
                        'view team', 'create team', 'edit team', 'delete team',
                        'view category', 'create category', 'edit category', 'delete category',
                        'view platform', 'create platform', 'edit platform', 'delete platform',
                        'view newsfeed', 'create newsfeed', 'edit newsfeed', 'delete newsfeed',
                        'view social media report', 'create social media report', 'edit social media report', 'delete social media report',
                        'view asset', 'create asset', 'delete asset',
                        'view analytics'
                    ]
                ],
                'content_writer' => [
                    'name' => 'Content Writer',
                    'permissions' => [
                        'view task', 'edit task',
                        'view calendar',
                        'view asset', 'create asset'
                    ]
                ],
                'designer' => [
                    'name' => 'Designer',
                    'permissions' => [
                        'view task', 'edit task',
                        'view calendar',
                        'view asset', 'create asset', 'edit asset'
                    ]
                ],
                'video_editor' => [
                    'name' => 'Video Editor',
                    'permissions' => [
                        'view task', 'edit task',
                        'view calendar',
                        'view asset', 'create asset', 'edit asset'
                    ]
                ],
                'social_media' => [
                    'name' => 'Social Media',
                    'permissions' => [
                        'view task', 'edit task',
                        'view calendar', 'create calendar',
                        'view asset', 'create asset'
                    ]
                ]
            ];

            foreach ($roles as $roleKey => $roleData) {
                $role = Role::create(['name' => $roleData['name'], 'guard_name' => 'web']);
                
                // Get permissions for this role
                $rolePermissions = Permission::whereIn('name', $roleData['permissions'])->get();
                
                // Assign permissions to role
                $role->syncPermissions($rolePermissions);
                
                \Log::info("Created role {$roleData['name']} with " . count($rolePermissions) . " permissions");
            }

            \Log::info('All roles and permissions created successfully');
        } catch (\Exception $e) {
            \Log::error('Error creating roles and permissions: ' . $e->getMessage());
            throw $e;
        }
    }
}