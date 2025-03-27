<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $settings = $this->getSettings();
        
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'phone' => $request->user()->phone,
                    'avatar_url' => $request->user()->avatar_url,
                    'roles' => $request->user()->roles->pluck('name')->toArray(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name')->toArray(),
                    'status' => $request->user()->status,
                    'email_verified_at' => $request->user()->email_verified_at,
                    'last_login_at' => $request->user()->last_login_at?->diffForHumans(),
                    'is_admin' => $request->user()->hasRole('Super Admin'),
                    'is_content_manager' => $request->user()->hasRole('Content Manager'),
                    'profile_photo_url' => $request->user()->profile_photo_url,
                    'created_at' => $request->user()->created_at,
                ] : null
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
                'success' => fn () => $request->session()->get('success'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
                'url' => config('app.url'),
            ],
            'ziggy' => [
                'location' => $request->url(),
            ],
            'settings' => $settings,
        ]);
    }

    protected function getAuthData(Request $request): array
    {
        if (!$request->user()) {
            return [
                'user' => null,
            ];
        }

        $user = $request->user();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar_url' => $user->avatar_url,
                'roles' => $user->roles->pluck('name')->toArray(),
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                'status' => $user->status,
                'email_verified_at' => $user->email_verified_at,
                'last_login_at' => $user->last_login_at?->diffForHumans(),
                'is_admin' => $user->hasRole('Super Admin'),
                'is_content_manager' => $user->hasRole('Content Manager')
            ],
        ];
    }

    protected function getSettings()
    {
        return cache()->remember('website_settings', 3600, function () {
            $settings = Setting::pluck('value', 'key')->toArray();
            
            if (!empty($settings['site_title'])) {
                config(['app.name' => $settings['site_title']]);
            }
            
            return $settings;
        });
    }
}
