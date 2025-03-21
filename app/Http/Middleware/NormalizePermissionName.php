<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NormalizePermissionName
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array $permissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissions)
    {
        // Konversi permissoin tunggal menjadi array
        $permissionArray = is_array($permissions) ? $permissions : explode('|', $permissions);

        // Normalisasi permission
        $normalizedPermissions = [];
        foreach ($permissionArray as $permission) {
            // Konversi format dengan dash ke format dengan spasi
            $normalizedPermission = str_replace('-', ' ', trim($permission));
            $normalizedPermissions[] = $normalizedPermission;
            
            // Log untuk debugging
            Log::debug("Permission diubah: dari '$permission' menjadi '$normalizedPermission'");
        }

        // Gabungkan kembali dengan pipe separator
        $normalizedPermissionString = implode('|', $normalizedPermissions);

        // Ganti parameter permission yang ada di request
        $request->route()->setParameter('permission', $normalizedPermissionString);

        return $next($request);
    }
} 