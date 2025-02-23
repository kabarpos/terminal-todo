<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PermissionService;
use App\Services\GuardService;
use App\Services\PermissionCacheService;

class PermissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register services as singletons
        $this->app->singleton(PermissionService::class);
        $this->app->singleton(GuardService::class);
        $this->app->singleton(PermissionCacheService::class);
    }

    public function boot()
    {
        // Register cache invalidation listeners
        $this->app->make(PermissionCacheService::class)
            ->registerCacheInvalidationListeners();

        // Generate permissions in production
        if ($this->app->environment('production')) {
            $this->app->booted(function () {
                $this->app->make(PermissionService::class)
                    ->generatePermissions();
            });
        }

        // Register default guard if needed
        if ($this->app->environment('local')) {
            $this->app->make(GuardService::class)
                ->registerGuard('admin', [
                    'driver' => 'session',
                    'provider' => 'users',
                ]);
        }
    }
} 