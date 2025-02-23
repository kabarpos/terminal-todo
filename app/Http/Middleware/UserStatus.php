<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserStatus
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
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        switch ($user->status) {
            case 'pending':
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->with('error', 'Akun Anda masih dalam proses review. ' . ($user->status_reason ? "Alasan: {$user->status_reason}" : 'Silakan tunggu persetujuan dari admin.'));
            
            case 'rejected':
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->with('error', 'Akun Anda ditolak. ' . ($user->status_reason ? "Alasan: {$user->status_reason}" : ''));
            
            case 'banned':
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->with('error', 'Akun Anda telah dibanned. ' . ($user->status_reason ? "Alasan: {$user->status_reason}" : ''));
            
            case 'inactive':
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->with('error', 'Akun Anda sedang dinonaktifkan. ' . ($user->status_reason ? "Alasan: {$user->status_reason}" : ''));
            
            case 'active':
                return $next($request);
            
            default:
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->with('error', 'Status akun Anda tidak valid. Silakan hubungi admin.');
        }
    }
} 