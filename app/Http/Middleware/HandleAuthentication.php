<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Tambahkan role dan permission ke request
        $user = $request->user();
        $roles = $user->roles()->with('permissions')->get();
        $allPermissions = collect();
        
        foreach ($roles as $role) {
            $allPermissions = $allPermissions->merge($role->permissions);
        }
        
        // Tambahkan permission langsung dari user
        $allPermissions = $allPermissions->merge($user->permissions);
        
        // Tambahkan data ke request untuk digunakan di controller
        $request->attributes->add([
            'user_roles' => $roles->pluck('name')->toArray(),
            'user_permissions' => $allPermissions->pluck('name')->unique()->values()->all(),
            'is_admin' => $user->hasRole('Super Admin'),
            'is_content_manager' => $user->hasRole('Content Manager')
        ]);

        $response = $next($request);
        
        // Jika response adalah JSON dan memiliki data user, tambahkan role dan permission
        if (method_exists($response, 'getData')) {
            $data = $response->getData(true);
            if (isset($data['user'])) {
                $data['user'] = array_merge($data['user'], [
                    'roles' => $roles->map(function($role) {
                        return [
                            'name' => $role->name,
                            'permissions' => $role->permissions->pluck('name')
                        ];
                    })->toArray(),
                    'permissions' => $allPermissions->pluck('name')->unique()->values()->all(),
                    'is_admin' => $user->hasRole('Super Admin'),
                    'is_content_manager' => $user->hasRole('Content Manager')
                ]);
                $response->setData($data);
            }
        }

        return $response;
    }
} 