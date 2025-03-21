<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\CalendarComment;
use App\Policies\CalendarCommentPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        CalendarComment::class => CalendarCommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super Admin')) {
                \Log::info("User {$user->name} granted {$ability} via Super Admin role");
                return true;
            }
            return null;
        });

        // Tambahkan logging untuk permission check
        Gate::after(function ($user, $ability, $result, $arguments) {
            if ($result) {
                \Log::info("Permission check passed: User {$user->name} has {$ability}");
            } else {
                \Log::warning("Permission check failed: User {$user->name} doesn't have {$ability}");
            }
            return $result;
        });
    }
} 