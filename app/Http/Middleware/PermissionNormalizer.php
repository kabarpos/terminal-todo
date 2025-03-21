<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionNormalizer
{
    /**
     * Handle permintaan masuk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array $permissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissions = null)
    {
        Log::debug("PermissionNormalizer dijalankan dengan parameter: ", [
            'permissions' => $permissions,
            'url' => $request->url(),
            'method' => $request->method()
        ]);

        // Jika tidak ada permissions, lewati normalisasi
        if (!$permissions) {
            Log::debug("Tidak ada parameter permission, melewati normalisasi");
            return $next($request);
        }

        // Konversi permission tunggal menjadi array
        $permissionArray = is_array($permissions) ? $permissions : explode('|', $permissions);
        
        Log::debug("Permission array setelah explode: ", $permissionArray);

        // Normalisasi permission
        $normalizedPermissions = [];
        foreach ($permissionArray as $permission) {
            // Konversi format dengan dash ke format dengan spasi
            $normalizedPermission = str_replace('-', ' ', trim($permission));
            $normalizedPermissions[] = $normalizedPermission;
            
            // Log untuk debugging
            Log::debug("Permission dinormalisasi: dari '$permission' menjadi '$normalizedPermission'");
        }

        // Gabungkan kembali dengan pipe separator
        $normalizedPermissionString = implode('|', $normalizedPermissions);
        
        Log::debug("Permission string setelah normalisasi: $normalizedPermissionString");

        // Ganti parameter permission yang ada di request
        $request->route()->setParameter('permission', $normalizedPermissionString);

        return $next($request);
    }
} 