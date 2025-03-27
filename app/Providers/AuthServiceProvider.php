<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\CalendarComment;
use App\Policies\CalendarCommentPolicy;
use App\Models\User;

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

        // Register permission normalization
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('Super Admin')) {
                \Log::info("User {$user->name} granted {$ability} via Super Admin role");
                return true;
            }
            
            // Log untuk debugging
            \Log::debug("Checking permission: {$ability} for user: {$user->name}", [
                'user_id' => $user->id,
                'user_roles' => $user->roles->pluck('name'),
                'permission' => $ability
            ]);
            
            // Percobaan normalisasi permission - ini akan memeriksa varian permission
            $normalizedAbility = $this->normalizePermissionString($ability);
            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
            
            // Normalisasi semua permission user
            $normalizedUserPermissions = array_map(function($permission) {
                return $this->normalizePermissionString($permission);
            }, $userPermissions);
            
            \Log::debug("Normalized permission check", [
                'original' => $ability,
                'normalized' => $normalizedAbility,
                'has_direct_match' => in_array($normalizedAbility, $normalizedUserPermissions)
            ]);
            
            // Cek apakah user memiliki permission yang dinormalisasi
            if (in_array($normalizedAbility, $normalizedUserPermissions)) {
                return true;
            }
            
            // Jangan return false, biarkan proses normal berlanjut
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

    /**
     * Normalisasi string permission agar konsisten
     *
     * @param string $permission
     * @return string
     */
    private function normalizePermissionString($permission)
    {
        // Hapus semua spasi ekstra
        $permission = trim(preg_replace('/\s+/', ' ', $permission));
        
        // Coba deteksi format: apakah ini format "action resource" atau "resource-action"
        if (strpos($permission, '-') !== false) {
            // Format "action-resource" atau "resource-action"
            $parts = explode('-', $permission);
            
            // Jika ada lebih dari 2 bagian, ambil 2 bagian utama
            if (count($parts) > 2) {
                // Format "action-multi-word-resource"
                $action = $parts[0];
                $resource = implode('-', array_slice($parts, 1));
                $parts = [$action, $resource];
            }
            
            // Deteksi apakah formatnya "action-resource" atau "resource-action"
            $firstPart = $parts[0];
            $commonActions = ['view', 'create', 'edit', 'delete', 'manage', 'export', 'import'];
            
            if (in_array($firstPart, $commonActions)) {
                // Format "action-resource"
                return strtolower($firstPart . '-' . $parts[1]);
            } else {
                // Format "resource-action"
                return strtolower($parts[1] . '-' . $firstPart);
            }
        } else if (strpos($permission, ' ') !== false) {
            // Format "action resource"
            $parts = explode(' ', $permission);
            return strtolower($parts[0] . '-' . $parts[1]);
        }
        
        // Format lain atau tidak bisa dinormalisasi
        return strtolower($permission);
    }
} 