<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
} 