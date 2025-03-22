<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\PermissionHelper;
use Symfony\Component\HttpFoundation\Response;

class FlexiblePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissions)
    {
        if (Auth::guest()) {
            abort(403);
        }

        $user = Auth::user();
        
        // Log pemeriksaan permission untuk debugging
        Log::debug('[FlexiblePermission] Checking permissions', [
            'requested_permissions' => $permissions,
            'user' => $user->name,
            'user_id' => $user->id
        ]);
        
        // Jika user adalah Super Admin, selalu izinkan
        if ($user->hasRole('Super Admin')) {
            Log::debug('[FlexiblePermission] Super Admin access granted');
            return $next($request);
        }

        // Pisahkan permission jika ada multiple (dipisahkan dengan |)
        $permissionArray = explode('|', $permissions);
        
        // Periksa setiap permission
        foreach ($permissionArray as $permission) {
            if (PermissionHelper::userHasPermission($user, $permission)) {
                Log::debug('[FlexiblePermission] Permission granted', [
                    'permission' => $permission
                ]);
                return $next($request);
            }
        }

        // Jika yang diminta adalah JSON, kirim response JSON
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Anda tidak memiliki izin untuk mengakses resource ini.'], 403);
        }

        // Jika tidak ada permission yang cocok, abort
        Log::warning('[FlexiblePermission] Permission denied', [
            'user' => $user->name,
            'permissions' => $permissionArray,
            'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray()
        ]);
        
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
} 