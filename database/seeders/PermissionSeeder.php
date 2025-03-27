<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Kelompokkan permission berdasarkan modul untuk pengelolaan yang lebih terorganisir
        $permissionsByModule = [
            'users' => [
                'view-users',
                'create-users',
                'edit-users',
                'delete-users',
                'manage-users',
            ],
            'roles' => [
                'view-roles',
                'create-roles',
                'edit-roles',
                'delete-roles',
                'manage-roles',
            ],
            'tasks' => [
                'view-task',
                'create-task',
                'edit-task',
                'delete-task',
                'manage-task',
            ],
            'teams' => [
                'view-team',
                'create-team',
                'edit-team',
                'delete-team',
                'manage-team',
            ],
            'calendar' => [
                'view-calendar',
                'create-calendar',
                'edit-calendar',
                'delete-calendar',
                'manage-calendar',
            ],
            'category' => [
                'view-category',
                'create-category',
                'edit-category',
                'delete-category',
                'manage-category',
            ],
            'platform' => [
                'view-platform',
                'create-platform',
                'edit-platform',
                'delete-platform',
                'manage-platform',
            ],
            'newsfeed' => [
                'view-newsfeed',
                'create-newsfeed',
                'edit-newsfeed',
                'delete-newsfeed',
                'manage-newsfeed',
            ],
            'social media report' => [
                'view-social-media-report',
                'create-social-media-report',
                'edit-social-media-report',
                'delete-social-media-report',
                'manage-social-media-report',
                'export-social-media-report',
            ],
            'metric data' => [
                'view-metric-data',
                'create-metric-data',
                'edit-metric-data',
                'delete-metric-data',
                'manage-metric-data',
                'import-metric-data',
                'export-metric-data',
            ],
            'analytics' => [
                'view-analytics',
                'export-analytics',
            ],
            'settings' => [
                'view-settings',
                'edit-settings',
                'manage-settings',
            ],
            'media' => [
                'view-media',
                'create-media',
                'edit-media',
                'delete-media',
                'manage-media',
            ],
            'social platform' => [
                'view-social-platform',
                'create-social-platform',
                'edit-social-platform',
                'delete-social-platform',
                'manage-social-platform',
            ],
            'social account' => [
                'view-social-account',
                'create-social-account',
                'edit-social-account',
                'delete-social-account',
                'manage-social-account',
            ],
        ];

        // Masukkan semua permission ke dalam database
        foreach ($permissionsByModule as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
                $this->command->info('Permission ' . $permission . ' created');
            }
        }

        // Update Permission Object dalam Cache
        $this->command->info('Clearing permission cache...');
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info('Permission cache cleared!');
    }
} 