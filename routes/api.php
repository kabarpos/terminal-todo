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
    return response()->json(['token' => csrf_token()]);
});

// Route untuk autentikasi
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Existing routes
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    
    // New routes
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('platforms', PlatformController::class);
}); 