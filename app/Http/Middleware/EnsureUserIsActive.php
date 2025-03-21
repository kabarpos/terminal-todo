<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isActive()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun Anda belum aktif atau telah dinonaktifkan. Silakan hubungi administrator.'
                ], 403);
            }

            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum aktif atau telah dinonaktifkan. Silakan hubungi administrator.');
        }

        return $next($request);
    }
} 