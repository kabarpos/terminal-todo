<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\EditorialCalendarController;
use App\Http\Controllers\CalendarCommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsFeedController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AnalyticsController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tasks Routes
    Route::resource('tasks', TaskController::class);
    Route::put('tasks/{task}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    
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
        Route::resource('calendar', EditorialCalendarController::class);

        // Calendar Comments Routes
        Route::post('calendar/{calendar}/comments', [CalendarCommentController::class, 'store'])->name('calendar.comments.store');
        Route::delete('calendar/{calendar}/comments/{comment}', [CalendarCommentController::class, 'destroy'])->name('calendar.comments.destroy');
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

    Route::resource('news-feeds', NewsFeedController::class);
    Route::post('news-feeds/preview', [NewsFeedController::class, 'preview'])->name('news-feeds.preview');
    Route::post('/news-feeds/fetch-metadata', [NewsFeedController::class, 'fetchMetadata'])->name('news-feeds.fetch-metadata');

    Route::resource('teams', TeamController::class);
    Route::get('teams/{team}/available-users', [TeamController::class, 'getAvailableUsers'])->name('teams.available-users');
    Route::post('teams/{team}/members', [TeamController::class, 'addMember'])->name('teams.members.add');
    Route::delete('teams/{team}/members', [TeamController::class, 'removeMember'])->name('teams.members.remove');
    Route::put('teams/{team}/members', [TeamController::class, 'updateMemberRole'])->name('teams.members.update-role');

    // Assets Routes
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

    // Analytics Routes
    Route::get('analytics', [AnalyticsController::class, 'index'])
        ->name('analytics.index')
        ->middleware('permission:view analytics');

    // Social Media Reports Routes
    Route::resource('social-media-reports', \App\Http\Controllers\SocialMediaReportController::class)
        ->middleware('permission:view social media report')
        ->except(['show']);
    
    // Export route
    Route::get('social-media-reports/export', [\App\Http\Controllers\SocialMediaReportController::class, 'export'])
        ->name('social-media-reports.export')
        ->middleware('permission:view social media report');
});
