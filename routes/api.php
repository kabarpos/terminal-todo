<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route untuk mendapatkan CSRF token
Route::get('/csrf-token', function () {
    $token = csrf_token();
    return response()->json(['token' => $token])
        ->withHeaders([
            'X-CSRF-TOKEN' => $token,
        ])
        ->withCookie('XSRF-TOKEN', $token, config('session.lifetime'));
});

// Route untuk autentikasi
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    $roles = $user->roles()->with('permissions')->get();
    $allPermissions = collect();
    
    foreach ($roles as $role) {
        $allPermissions = $allPermissions->merge($role->permissions);
    }
    
    // Tambahkan permission langsung dari user
    $allPermissions = $allPermissions->merge($user->permissions);
    
    return response()->json([
        'user' => array_merge($user->toArray(), [
            'roles' => $roles->map(function($role) {
                return [
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')
                ];
            }),
            'permissions' => $allPermissions->pluck('name')->unique()->values()->all(),
            'is_admin' => $user->hasRole('Super Admin'),
            'is_content_manager' => $user->hasRole('Content Manager')
        ])
    ]);
});

// Route yang memerlukan autentikasi
Route::middleware(['auth:sanctum'])->group(function () {
    // Route untuk Super Admin
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::apiResource('users', UserController::class)->names([
            'index' => 'api.users.index',
            'store' => 'api.users.store',
            'show' => 'api.users.show',
            'update' => 'api.users.update',
            'destroy' => 'api.users.destroy',
        ]);
        
        Route::apiResource('roles', RoleController::class)->names([
            'index' => 'api.roles.index',
            'store' => 'api.roles.store',
            'show' => 'api.roles.show',
            'update' => 'api.roles.update',
            'destroy' => 'api.roles.destroy',
        ]);
    });

    // Route untuk Content Manager dan Super Admin
    Route::middleware(['role:Super Admin|Content Manager|Manager'])->group(function () {
        Route::apiResource('categories', CategoryController::class)->names([
            'index' => 'api.categories.index',
            'store' => 'api.categories.store',
            'show' => 'api.categories.show',
            'update' => 'api.categories.update',
            'destroy' => 'api.categories.destroy',
        ]);
        
        Route::apiResource('platforms', PlatformController::class)->names([
            'index' => 'api.platforms.index',
            'store' => 'api.platforms.store',
            'show' => 'api.platforms.show',
            'update' => 'api.platforms.update',
            'destroy' => 'api.platforms.destroy',
        ]);
    });

    // Route untuk semua user yang terautentikasi
    Route::apiResource('tasks', TaskController::class)->names([
        'index' => 'api.tasks.index',
        'store' => 'api.tasks.store',
        'show' => 'api.tasks.show',
        'update' => 'api.tasks.update',
        'destroy' => 'api.tasks.destroy',
    ]);
}); 