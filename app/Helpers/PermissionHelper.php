<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PermissionHelper
{
    /**
     * Check if the current user has the given permission
     * 
     * @param string|array $permissions The permission(s) to check
     * @param bool $requireAll Whether all permissions are required
     * @return bool
     */
    public static function hasPermission($permissions, bool $requireAll = false): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        if ($user->hasRole('Super Admin')) {
            Log::info("User {$user->name} granted permission via Super Admin role");
            return true;
        }

        $permissionList = is_array($permissions) ? $permissions : [$permissions];
        
        // Log for debugging
        Log::info("Checking permissions for user {$user->name}: ", [
            'permissions_to_check' => $permissionList,
            'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'require_all' => $requireAll,
        ]);

        // Perform the permission check
        $result = $requireAll 
            ? $user->hasAllPermissions($permissionList)
            : $user->hasAnyPermission($permissionList);

        Log::info("Permission check result for user {$user->name}: " . ($result ? 'Passed' : 'Failed'));
        
        return $result;
    }

    /**
     * Check if the current user has all the given permissions
     * 
     * @param string|array $permissions The permission(s) to check
     * @return bool
     */
    public static function hasAllPermissions($permissions): bool
    {
        return self::hasPermission($permissions, true);
    }

    /**
     * Check if the current user has any of the given permissions
     * 
     * @param string|array $permissions The permission(s) to check
     * @return bool
     */
    public static function hasAnyPermission($permissions): bool
    {
        return self::hasPermission($permissions, false);
    }

    /**
     * Get all permissions for the current user
     * 
     * @return array
     */
    public static function getUserPermissions(): array
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        
        Log::info("Retrieved permissions for user {$user->name}", [
            'permissions' => $permissions,
        ]);
        
        return $permissions;
    }

    /**
     * Menghasilkan semua kemungkinan varian dari sebuah permission string
     * 
     * @param string $permission
     * @return array
     */
    public static function generatePermissionVariants($permission)
    {
        $variants = collect();
        
        // Simpan versi asli
        $variants->push($permission);
        
        // Versi dengan tanda - dan spasi
        $withDash = Str::of($permission)->replace(' ', '-')->__toString();
        $withSpace = Str::of($permission)->replace('-', ' ')->__toString();
        
        $variants->push($withDash);
        $variants->push($withSpace);
        
        // Versi lowercase
        $variants->push(Str::lower($permission));
        $variants->push(Str::lower($withDash));
        $variants->push(Str::lower($withSpace));
        
        // Jika mengandung spasi, coba buat varian dengan urutan kata terbalik
        if (Str::contains($permission, ' ')) {
            $parts = explode(' ', $permission);
            if (count($parts) === 2) {
                $reversed = "{$parts[1]}-{$parts[0]}";
                $variants->push($reversed);
                $variants->push(Str::lower($reversed));
            }
        }
        
        // Jika mengandung tanda -, coba buat varian dengan urutan kata terbalik
        if (Str::contains($permission, '-')) {
            $parts = explode('-', $permission);
            if (count($parts) === 2) {
                $reversed = "{$parts[1]} {$parts[0]}";
                $variants->push($reversed);
                $variants->push(Str::lower($reversed));
            }
        }
        
        return $variants->unique()->values()->all();
    }
    
    /**
     * Periksa apakah user memiliki permission tertentu, mencakup semua varian
     * 
     * @param \App\Models\User $user
     * @param string $permission
     * @return bool
     */
    public static function userHasPermission($user, $permission)
    {
        // Jika user adalah admin, bypass semua permission
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        
        // Jika permission tidak dispesifikasikan
        if (empty($permission)) {
            return true;
        }
        
        // Dapatkan semua varian permission yang diminta
        $requestedVariants = self::generatePermissionVariants($permission);
        
        // Dapatkan semua permission yang dimiliki user
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        
        // Periksa setiap permission user
        foreach ($userPermissions as $userPerm) {
            $userPermVariants = self::generatePermissionVariants($userPerm);
            
            // Periksa apakah ada intersection
            foreach ($requestedVariants as $requestedVariant) {
                if (in_array($requestedVariant, $userPermVariants)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Normalisasi string permission menjadi format standar
     * 
     * @param string $permission
     * @return string
     */
    public static function normalizePermission($permission)
    {
        // Hilangkan spasi ekstra
        $permission = trim(preg_replace('/\s+/', ' ', $permission));
        
        // Pecah string permission
        if (Str::contains($permission, '-')) {
            // Format dengan tanda -
            $parts = explode('-', $permission);
            return self::normalizePermissionParts($parts);
        } else if (Str::contains($permission, ' ')) {
            // Format dengan spasi
            $parts = explode(' ', $permission);
            return self::normalizePermissionParts($parts);
        }
        
        // Tidak bisa dinormalisasi, kembalikan lowercase
        return Str::lower($permission);
    }
    
    /**
     * Normalisasi bagian-bagian dari string permission
     * 
     * @param array $parts
     * @return string
     */
    private static function normalizePermissionParts($parts)
    {
        if (count($parts) != 2) {
            // Jika bukan 2 bagian, gabungkan dengan tanda -
            return Str::lower(implode('-', $parts));
        }
        
        // Cek apakah bagian pertama adalah action
        $commonActions = ['view', 'create', 'edit', 'delete', 'manage', 'export', 'import'];
        
        if (in_array(Str::lower($parts[0]), $commonActions)) {
            // Format "action-resource"
            return Str::lower($parts[0] . '-' . $parts[1]);
        } else {
            // Format "resource-action"
            return Str::lower($parts[1] . '-' . $parts[0]);
        }
    }
} 