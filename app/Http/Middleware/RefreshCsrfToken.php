<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class RefreshCsrfToken
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Jika response adalah JSON dan token CSRF expired
        if ($this->shouldRefreshToken($request, $response)) {
            // Refresh CSRF token
            $token = csrf_token();
            
            // Tambahkan token ke response header
            $response->header('X-CSRF-TOKEN', $token);
            
            // Set cookie baru
            $response->withCookie(
                Cookie::make('XSRF-TOKEN', $token, config('session.lifetime'), '/', null, config('session.secure'), true)
            );
        }

        return $response;
    }

    protected function shouldRefreshToken($request, $response)
    {
        return $response->getStatusCode() === 419 || 
               ($request->ajax() && $response->getStatusCode() === 200);
    }
} 