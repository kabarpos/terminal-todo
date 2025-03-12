<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewPermissionSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing data
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();

        // Enable foreign key checks back
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Define permission groups
        $permissionGroups = [
            'dashboard' => [
                'view-dashboard' => 'Akses ke dashboard',
            ],
            'calendar' => [
                'view-calendar' => 'Melihat kalender',
                'manage-calendar' => 'Mengelola kalender',
            ],
            'task' => [
                'view-task' => 'Melihat tugas',
                'manage-task' => 'Mengelola tugas',
                'create-task' => 'Membuat tugas',
                'edit-task' => 'Mengedit tugas',
                'delete-task' => 'Menghapus tugas',
            ],
            'team' => [
                'view-team' => 'Melihat tim',
                'manage-team' => 'Mengelola tim',
                'create-team' => 'Membuat tim',
                'edit-team' => 'Mengedit tim',
                'delete-team' => 'Menghapus tim',
            ],
            'category' => [
                'view-category' => 'Melihat kategori',
                'manage-category' => 'Mengelola kategori',
            ],
            'platform' => [
                'view-platform' => 'Melihat platform',
                'manage-platform' => 'Mengelola platform',
            ],
            'newsfeed' => [
                'view-newsfeed' => 'Melihat news feed',
                'manage-newsfeed' => 'Mengelola news feed',
            ],
            'social-media-report' => [
                'view-social-media-report' => 'Melihat laporan media sosial',
                'manage-social-media-report' => 'Mengelola laporan media sosial',
            ],
            'asset' => [
                'view-asset' => 'Melihat aset',
                'manage-asset' => 'Mengelola aset',
                'create-asset' => 'Mengunggah aset',
                'delete-asset' => 'Menghapus aset',
            ],
            'analytics' => [
                'view-analytics' => 'Melihat analytics',
                'export-analytics' => 'Mengekspor data analytics',
            ],
            'media' => [
                'view-media' => 'Melihat media library',
                'manage-media' => 'Mengelola media library',
            ],
            'user' => [
                'view-users' => 'Melihat pengguna',
                'manage-users' => 'Mengelola pengguna',
                'create-user' => 'Membuat pengguna',
                'edit-user' => 'Mengedit pengguna',
                'delete-user' => 'Menghapus pengguna',
            ],
            'role' => [
                'view-roles' => 'Melihat role',
                'manage-roles' => 'Mengelola role',
                'create-role' => 'Membuat role',
                'edit-role' => 'Mengedit role',
                'delete-role' => 'Menghapus role',
            ],
            'setting' => [
                'manage-settings' => 'Mengelola pengaturan',
            ],
            'metric-data' => [
                'view-metric-data' => 'Melihat data metrik',
                'manage-metric-data' => 'Mengelola data metrik',
                'export-metric-data' => 'Mengekspor data metrik',
            ],
        ];

        // Create permissions
        $allPermissions = [];
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $slug => $description) {
                $permission = DB::table('permissions')->insertGetId([
                    'name' => $slug,
                    'slug' => $slug,
                    'group' => $group,
                    'description' => $description,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $allPermissions[$slug] = $permission;
            }
        }

        // Define roles
        $roles = [
            'super-admin' => [
                'name' => 'Super Admin',
                'description' => 'Full access to all features',
                'is_system' => true,
                'permissions' => array_values($allPermissions), // All permissions
            ],
            'content-manager' => [
                'name' => 'Content Manager',
                'description' => 'Manage all content and team related features',
                'is_system' => false,
                'permissions' => $this->getPermissionIds($allPermissions, [
                    'view-dashboard',
                    'manage-calendar',
                    'manage-task',
                    'manage-team',
                    'manage-asset',
                    'view-analytics',
                    'manage-social-media-report',
                ]),
            ],
            'content-creator' => [
                'name' => 'Content Creator',
                'description' => 'Create and manage content',
                'is_system' => false,
                'permissions' => $this->getPermissionIds($allPermissions, [
                    'view-dashboard',
                    'view-calendar',
                    'view-task',
                    'create-task',
                    'edit-task',
                    'view-team',
                    'view-asset',
                    'create-asset',
                ]),
            ],
            'analyst' => [
                'name' => 'Analyst',
                'description' => 'View and analyze content performance',
                'is_system' => false,
                'permissions' => $this->getPermissionIds($allPermissions, [
                    'view-dashboard',
                    'view-calendar',
                    'view-analytics',
                    'export-analytics',
                    'view-social-media-report',
                ]),
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $slug => $role) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => $role['name'],
                'slug' => $slug,
                'description' => $role['description'],
                'is_system' => $role['is_system'],
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign permissions to role
            foreach ($role['permissions'] as $permissionId) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function getPermissionIds($allPermissions, $slugs)
    {
        return array_values(array_intersect_key($allPermissions, array_flip($slugs)));
    }
} 