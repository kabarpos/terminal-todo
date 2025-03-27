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
                'view-dashboard',
                
                // Calendar
                'view-calendar',
                'create-calendar',
                'edit-calendar',
                'delete-calendar',
                
                // Tasks
                'view-task',
                'create-task',
                'edit-task',
                'delete-task',
                
                // Teams
                'view-team',
                'create-team',
                'edit-team',
                'delete-team',
                
                // Categories
                'view-category',
                'create-category',
                'edit-category',
                'delete-category',
                
                // Platforms
                'view-platform',
                'create-platform',
                'edit-platform',
                'delete-platform',
                
                // Media
                'view-media',
                'create-media',
                'edit-media',
                'delete-media',
                'manage-media',
                
                // Settings
                'view-settings',
                'edit-settings',
                'manage-settings',
                
                // News Feed
                'view-newsfeed',
                'create-newsfeed',
                'edit-newsfeed',
                'delete-newsfeed',
                'manage-newsfeed',
                
                // Social Media Reports
                'view-social-media-report',
                'create-social-media-report',
                'edit-social-media-report',
                'delete-social-media-report',
                'manage-social-media-report',
                
                // Social Platforms
                'view-social-platform',
                'create-social-platform',
                'edit-social-platform',
                'delete-social-platform',
                'manage-social-platform',
                
                // Social Accounts
                'view-social-account',
                'create-social-account',
                'edit-social-account',
                'delete-social-account',
                'manage-social-account',
                
                // Metric Data
                'view-metric-data',
                'create-metric-data',
                'edit-metric-data',
                'delete-metric-data',
                'manage-metric-data',
                
                // Analytics
                'view-analytics',
                'create-analytics',
                'edit-analytics',
                'delete-analytics',
                'manage-analytics',
                
                // Admin permissions
                'view-users',
                'create-users',
                'edit-users',
                'delete-users',
                'view-roles',
                'create-roles',
                'edit-roles',
                'delete-roles',
            ];

            // Create all permissions
            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission]);
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
                        'view-dashboard',
                        'view-calendar', 'create-calendar', 'edit-calendar', 'delete-calendar',
                        'view-task', 'create-task', 'edit-task', 'delete-task',
                        'view-team', 'create-team', 'edit-team', 'delete-team',
                        'view-category', 'create-category', 'edit-category', 'delete-category',
                        'view-platform', 'create-platform', 'edit-platform', 'delete-platform',
                        'view-media', 'create-media', 'edit-media', 'delete-media', 'manage-media',
                        'view-settings', 'edit-settings', 'manage-settings',
                        'view-newsfeed', 'create-newsfeed', 'edit-newsfeed', 'delete-newsfeed', 'manage-newsfeed',
                        'view-social-media-report', 'create-social-media-report', 'edit-social-media-report', 'delete-social-media-report', 'manage-social-media-report',
                        'view-social-platform', 'create-social-platform', 'edit-social-platform', 'delete-social-platform', 'manage-social-platform',
                        'view-social-account', 'create-social-account', 'edit-social-account', 'delete-social-account', 'manage-social-account',
                        'view-metric-data', 'create-metric-data', 'edit-metric-data', 'delete-metric-data', 'manage-metric-data',
                        'view-analytics', 'create-analytics', 'edit-analytics', 'delete-analytics', 'manage-analytics',
                    ]
                ],
                'content_writer' => [
                    'name' => 'Content Writer',
                    'permissions' => [
                        'view-task', 'edit-task',
                        'view-calendar',
                        'view-media', 'create-media', 'edit-media',
                        'view-settings', 'edit-settings', 'manage-settings',
                        'view-newsfeed', 'create-newsfeed', 'edit-newsfeed',
                        'view-social-media-report', 'create-social-media-report', 'edit-social-media-report',
                        'view-social-platform', 'create-social-platform', 'edit-social-platform',
                        'view-social-account', 'create-social-account',
                        'view-metric-data', 'create-metric-data',
                        'view-analytics', 'create-analytics',
                    ]
                ],
                'designer' => [
                    'name' => 'Designer',
                    'permissions' => [
                        'view-task', 'edit-task',
                        'view-calendar',
                        'view-media', 'create-media', 'edit-media',
                        'view-settings', 'edit-settings', 'manage-settings',
                        'view-newsfeed', 'create-newsfeed', 'edit-newsfeed',
                        'view-social-media-report', 'create-social-media-report', 'edit-social-media-report',
                        'view-social-platform', 'create-social-platform', 'edit-social-platform',
                        'view-social-account', 'create-social-account',
                        'view-metric-data', 'create-metric-data',
                        'view-analytics', 'create-analytics',
                    ]
                ],
                'video_editor' => [
                    'name' => 'Video Editor',
                    'permissions' => [
                        'view-task', 'edit-task',
                        'view-calendar',
                        'view-media', 'create-media', 'edit-media',
                        'view-settings', 'edit-settings', 'manage-settings',
                        'view-newsfeed', 'create-newsfeed', 'edit-newsfeed',
                        'view-social-media-report', 'create-social-media-report', 'edit-social-media-report',
                        'view-social-platform', 'create-social-platform', 'edit-social-platform',
                        'view-social-account', 'create-social-account',
                        'view-metric-data', 'create-metric-data',
                        'view-analytics', 'create-analytics',
                    ]
                ],
                'social_media' => [
                    'name' => 'Social Media',
                    'permissions' => [
                        'view-task', 'edit-task',
                        'view-calendar', 'create-calendar',
                        'view-media', 'create-media',
                        'view-settings', 'edit-settings', 'manage-settings',
                        'view-newsfeed', 'create-newsfeed', 'edit-newsfeed',
                        'view-social-media-report', 'create-social-media-report', 'edit-social-media-report',
                        'view-social-platform', 'create-social-platform',
                        'view-social-account', 'create-social-account',
                        'view-metric-data', 'create-metric-data',
                        'view-analytics', 'create-analytics',
                    ]
                ]
            ];

            foreach ($roles as $roleKey => $roleData) {
                $role = Role::create(['name' => $roleData['name']]);
                $role->syncPermissions($roleData['permissions']);
                \Log::info("Created role {$roleData['name']} with " . count($roleData['permissions']) . " permissions");
            }

            \Log::info('All roles and permissions created successfully');
        } catch (\Exception $e) {
            \Log::error('Error creating roles and permissions: ' . $e->getMessage());
            throw $e;
        }
    }
}