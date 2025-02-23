<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionCacheService
{
    protected string $cachePrefix = 'permissions_';
    protected int $cacheDuration = 3600; // 1 hour

    /**
     * Get cached user permissions
     */
    public function getUserPermissions($user, string $guard = null): array
    {
        $cacheKey = $this->getUserPermissionsCacheKey($user->id, $guard);
        
        return Cache::tags(['permissions', 'user.'.$user->id])->remember(
            $cacheKey,
            $this->cacheDuration,
            function () use ($user, $guard) {
                return $user->getAllPermissions($guard)->pluck('name')->toArray();
            }
        );
    }

    /**
     * Get cached role permissions
     */
    public function getRolePermissions(Role $role): array
    {
        $cacheKey = $this->getRolePermissionsCacheKey($role->id);
        
        return Cache::tags(['permissions', 'role.'.$role->id])->remember(
            $cacheKey,
            $this->cacheDuration,
            function () use ($role) {
                return $role->permissions->pluck('name')->toArray();
            }
        );
    }

    /**
     * Check if user has permission (using cache)
     */
    public function userHasPermission($user, string $permission, string $guard = null): bool
    {
        $permissions = $this->getUserPermissions($user, $guard);
        return in_array($permission, $permissions);
    }

    /**
     * Clear user permissions cache
     */
    public function clearUserPermissionsCache($user): void
    {
        Cache::tags(['permissions', 'user.'.$user->id])->flush();
    }

    /**
     * Clear role permissions cache
     */
    public function clearRolePermissionsCache(Role $role): void
    {
        Cache::tags(['permissions', 'role.'.$role->id])->flush();
    }

    /**
     * Clear all permissions cache
     */
    public function clearAllPermissionsCache(): void
    {
        Cache::tags(['permissions'])->flush();
    }

    /**
     * Get cache key for user permissions
     */
    protected function getUserPermissionsCacheKey(int $userId, ?string $guard): string
    {
        return $this->cachePrefix . 'user_' . $userId . ($guard ? '_' . $guard : '');
    }

    /**
     * Get cache key for role permissions
     */
    protected function getRolePermissionsCacheKey(int $roleId): string
    {
        return $this->cachePrefix . 'role_' . $roleId;
    }

    /**
     * Register cache invalidation listeners
     */
    public function registerCacheInvalidationListeners(): void
    {
        // Listen for permission changes
        Permission::saved(function ($permission) {
            $this->clearAllPermissionsCache();
        });

        Permission::deleted(function ($permission) {
            $this->clearAllPermissionsCache();
        });

        // Listen for role permission changes
        Role::saved(function ($role) {
            $this->clearRolePermissionsCache($role);
        });

        Role::deleted(function ($role) {
            $this->clearRolePermissionsCache($role);
        });
    }
} 