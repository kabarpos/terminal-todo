<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    /**
     * Permission hierarchy definitions
     */
    protected array $permissionHierarchy = [
        'manage' => ['view', 'create', 'edit', 'delete'],
        'edit' => ['view'],
        'delete' => ['view'],
        'create' => ['view']
    ];

    /**
     * Generate permissions based on routes
     */
    public function generatePermissions(): array
    {
        $routes = Route::getRoutes();
        $permissions = [];
        
        foreach ($routes as $route) {
            if ($this->shouldSkipRoute($route)) {
                continue;
            }
            
            $routePermissions = $this->generatePermissionsForRoute($route);
            $permissions = array_merge(
                $permissions,
                $routePermissions,
                $this->generateInheritedPermissions($routePermissions)
            );
        }
        
        // Create permissions if not exists
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Invalidate permission cache
        $this->clearPermissionCache();
        
        return $permissions;
    }
    
    /**
     * Generate permissions for a single route
     */
    protected function generatePermissionsForRoute($route): array
    {
        $permissions = [];
        $routeName = $route->getName();
        
        if (!$routeName) {
            return [];
        }
        
        // Remove common prefixes like 'admin.'
        $baseRouteName = Str::after($routeName, 'admin.');
        
        // Split route name into segments
        $segments = explode('.', $baseRouteName);
        
        // Get the resource name (usually the first segment)
        $resource = $segments[0];
        
        // Get the action (usually the last segment)
        $action = end($segments);
        
        // Map route actions to permission verbs
        $permissionMap = [
            'index' => 'view',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'destroy' => 'delete'
        ];
        
        if (isset($permissionMap[$action])) {
            $permissions[] = $permissionMap[$action] . ucfirst($resource);
        } else {
            // For custom actions
            $permissions[] = $action . ucfirst($resource);
        }
        
        return $permissions;
    }
    
    /**
     * Check if route should be skipped for permission generation
     */
    protected function shouldSkipRoute($route): bool
    {
        $skipPrefixes = ['_', 'sanctum', 'ignition', 'horizon'];
        $skipNames = ['login', 'logout', 'register', 'password'];
        
        // Skip routes without names
        if (!$route->getName()) {
            return true;
        }
        
        // Skip routes with certain prefixes
        foreach ($skipPrefixes as $prefix) {
            if (Str::startsWith($route->getName(), $prefix)) {
                return true;
            }
        }
        
        // Skip authentication routes
        foreach ($skipNames as $name) {
            if (Str::contains($route->getName(), $name)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get module permissions structure for UI
     */
    public function getModulePermissions(): array
    {
        $permissions = Permission::all();
        $modules = [];
        
        foreach ($permissions as $permission) {
            // Extract module name using regex
            preg_match('/^(view|create|edit|delete|manage)(.+)$/', $permission->name, $matches);
            
            if (count($matches) >= 3) {
                $action = $matches[1];
                $module = Str::snake($matches[2]);
                
                if (!isset($modules[$module])) {
                    $modules[$module] = [
                        'name' => Str::title(str_replace('_', ' ', $module)),
                        'permissions' => []
                    ];
                }
                
                $modules[$module]['permissions'][] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'action' => $action
                ];
            }
        }
        
        return $modules;
    }

    /**
     * Generate inherited permissions based on hierarchy
     */
    protected function generateInheritedPermissions(array $permissions): array
    {
        $inherited = [];
        
        foreach ($permissions as $permission) {
            // Extract action and resource from permission name
            preg_match('/^(manage|view|create|edit|delete)(.+)$/', $permission, $matches);
            
            if (count($matches) >= 3) {
                $action = $matches[1];
                $resource = $matches[2];
                
                // If this is a parent permission (e.g., manage)
                if (isset($this->permissionHierarchy[$action])) {
                    // Add all child permissions
                    foreach ($this->permissionHierarchy[$action] as $childAction) {
                        $inherited[] = $childAction . $resource;
                    }
                }
            }
        }
        
        return array_unique($inherited);
    }

    /**
     * Check if a user has permission including inheritance
     */
    public function hasPermissionIncludingInheritance($user, string $permission): bool
    {
        // Check direct permission first
        if ($user->hasPermissionTo($permission)) {
            return true;
        }

        // Extract action and resource
        preg_match('/^(view|create|edit|delete)(.+)$/', $permission, $matches);
        
        if (count($matches) >= 3) {
            $action = $matches[1];
            $resource = $matches[2];
            
            // Check if user has parent permissions
            foreach ($this->permissionHierarchy as $parentAction => $childActions) {
                if (in_array($action, $childActions)) {
                    $parentPermission = $parentAction . $resource;
                    if ($user->hasPermissionTo($parentPermission)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get all permissions including inherited ones for a specific resource
     */
    public function getResourcePermissions(string $resource): array
    {
        $permissions = [];
        $baseActions = ['view', 'create', 'edit', 'delete', 'manage'];
        
        foreach ($baseActions as $action) {
            $permissions[$action] = [
                'name' => $action . ucfirst($resource),
                'inherits' => isset($this->permissionHierarchy[$action]) 
                    ? array_map(fn($a) => $a . ucfirst($resource), $this->permissionHierarchy[$action])
                    : []
            ];
        }
        
        return $permissions;
    }

    /**
     * Clear permission cache
     */
    protected function clearPermissionCache(): void
    {
        Cache::tags(['permissions', 'spatie.permission.cache'])->flush();
    }
} 