<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Routes
Route::get('/', function () {
    return Inertia::render('Public/Home');
})->name('home');

// Auth Routes (generated by Breeze)
require __DIR__.'/auth.php';

// Protected Routes
Route::middleware(['auth', 'verified', 'user_status'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');

    // Tasks Routes
    Route::resource('tasks', TaskController::class);
    
    // Task Comments Routes
    Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
    Route::put('tasks/comments/{comment}', [TaskCommentController::class, 'update'])->name('tasks.comments.update');
    Route::delete('tasks/comments/{comment}', [TaskCommentController::class, 'destroy'])->name('tasks.comments.destroy');

    // Profile Routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Content Management Routes
    Route::middleware(['role:Super Admin|Content Manager'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('platforms', PlatformController::class);
    });

    // Admin Routes
    Route::middleware('role:Super Admin')->prefix('admin')->name('admin.')->group(function () {
        // Users Management
        Route::resource('users', UserController::class);
        Route::put('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::put('users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::put('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::put('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
        // Roles Management
        Route::resource('roles', RoleController::class);
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});
