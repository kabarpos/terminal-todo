<?php

// Script untuk memperbaiki permissions di aplikasi Laravel
// Simpan file ini di root aplikasi, kemudian jalankan dengan: php fix_permissions.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

// Pastikan role 'Super Admin' ada
$superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

// Tambahkan permission baru yang diperlukan dengan format 'manage-social-media-report'
$newPermissions = [
    'manage-social-media-report',
    'view-social-media-report',
    'manage-task',
    'view-task',
    'manage-category',
    'view-category',
    'manage-team',
    'view-team',
    'manage-platform',
    'view-platform',
    'manage-newsfeed',
    'view-newsfeed',
    'manage-roles',
    'view-roles',
    'manage-users',
    'view-users',
    'manage-settings',
    'view-dashboard',
    'view-calendar',
    'manage-calendar',
    'view-analytics',
    'export-analytics',
    'view-asset',
    'manage-asset',
    'edit-task',
    'delete-task',
    'create-task',
    'edit-social-media-report',
    'delete-social-media-report',
    'create-social-media-report'
];

// Tambahkan permissions jika belum ada
foreach($newPermissions as $permName) {
    try {
        $perm = Permission::firstOrCreate([
            'name' => $permName,
            'guard_name' => 'web'
        ]);
        // Pastikan Super Admin memiliki permission ini
        if(!$superAdminRole->hasPermissionTo($permName)) {
            $superAdminRole->givePermissionTo($permName);
            echo "Ditambahkan permission: {$permName} ke Super Admin\n";
        }
    } catch (\Exception $e) {
        echo "Error menambahkan {$permName}: " . $e->getMessage() . "\n";
    }
}

// Cari user dengan email admin@example.com atau admin lain
$adminEmails = ['admin@example.com', 'superadmin@example.com', 'admin@admin.com'];
$userFound = false;

foreach ($adminEmails as $email) {
    $user = User::where('email', $email)->first();
    if ($user) {
        $user->assignRole('Super Admin');
        echo "Berhasil memberikan role Super Admin ke user: {$user->email}\n";
        $userFound = true;
        break;
    }
}

if (!$userFound) {
    echo "Tidak ditemukan user admin. Mohon buat user admin terlebih dahulu.\n";
}

// Tambahkan juga izin dengan format 'view social media report' (dengan spasi)
$spacePermissions = [
    'view social media report',
    'create social media report',
    'edit social media report',
    'delete social media report',
    'manage social media report',
    'view task',
    'create task',
    'edit task',
    'delete task',
    'manage task',
    'view dashboard'
];

foreach($spacePermissions as $permName) {
    try {
        $perm = Permission::firstOrCreate([
            'name' => $permName,
            'guard_name' => 'web'
        ]);
        // Pastikan Super Admin memiliki permission ini
        if(!$superAdminRole->hasPermissionTo($permName)) {
            $superAdminRole->givePermissionTo($permName);
            echo "Ditambahkan permission: {$permName} ke Super Admin\n";
        }
    } catch (\Exception $e) {
        echo "Error menambahkan {$permName}: " . $e->getMessage() . "\n";
    }
}

echo "\nProses selesai! Semua permission sudah ditambahkan ke role Super Admin.\n"; 