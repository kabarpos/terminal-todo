<?php

// Script untuk memastikan semua permission menggunakan format dash
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "=== SCRIPT FIX PERMISSION ===\n\n";

try {
    // 1. Perbaiki format permission dari spasi menjadi dash
    $permissions = Permission::all();
    $fixedCount = 0;

    echo "1. Memperbaiki format permission dari spasi menjadi dash...\n";
    
    foreach ($permissions as $permission) {
        $oldName = $permission->name;
        
        // Cek apakah permission berisi spasi
        if (strpos($oldName, ' ') !== false) {
            // Ubah format spasi menjadi dash
            $newName = str_replace(' ', '-', $oldName);
            
            echo "   Mengubah: {$oldName} -> {$newName}\n";
            
            // Update permission
            $permission->name = $newName;
            $permission->save();
            $fixedCount++;
        }
    }

    echo "   Total {$fixedCount} permission telah diperbaiki formatnya.\n\n";
    
    // 2. Periksa keberadaan Super Admin role dan usernya
    echo "2. Memeriksa role Super Admin...\n";
    
    $superAdminRole = Role::where('name', 'Super Admin')->first();
    
    if ($superAdminRole) {
        echo "   Role Super Admin ditemukan dengan ID: {$superAdminRole->id}\n";
        
        // 3. Pastikan Super Admin memiliki semua permission
        $allPermissions = Permission::all()->pluck('name')->toArray();
        $superAdminRole->syncPermissions($allPermissions);
        
        echo "   Role Super Admin telah diberikan semua permission (" . count($allPermissions) . " permission)\n";
        
        // 4. Periksa user dengan role Super Admin
        $superAdmins = User::role('Super Admin')->get();
        
        echo "   Ditemukan " . count($superAdmins) . " user dengan role Super Admin:\n";
        
        foreach ($superAdmins as $admin) {
            echo "   - {$admin->name} ({$admin->email})\n";
        }
    } else {
        echo "   ERROR: Role Super Admin tidak ditemukan!\n";
    }
    
    // 5. Reset cache permission
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    
    echo "\n3. Cache permission telah direset.\n";
    
    echo "\n=== SCRIPT SELESAI ===\n";
    echo "Silakan refresh aplikasi dan coba akses kembali.\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} 