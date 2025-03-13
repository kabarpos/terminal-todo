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
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\SocialPlatformController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\MetricDataController;
use App\Http\Controllers\SocialMediaAnalyticsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Routes
Route::get('/', function () {
    return Inertia::render('Public/Home');
})->name('home');

// Auth Routes (generated by Breeze)
require __DIR__.'/auth.php';

// Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view-dashboard')
        ->name('dashboard');

    // Tasks Routes
    Route::resource('tasks', TaskController::class)
        ->middleware(['permission:view-task|manage-task']);

    Route::put('tasks/{task}/update-status', [TaskController::class, 'updateStatus'])
        ->name('tasks.update-status')
        ->middleware('permission:manage-task');
    
    // Task Comments Routes
    Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store'])
        ->name('tasks.comments.store')
        ->middleware('permission:manage-task');
    
    Route::put('tasks/comments/{comment}', [TaskCommentController::class, 'update'])
        ->name('tasks.comments.update')
        ->middleware('permission:manage-task');
    
    Route::delete('tasks/comments/{comment}', [TaskCommentController::class, 'destroy'])
        ->name('tasks.comments.destroy')
        ->middleware('permission:manage-task');

    // Profile Routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Content Management Routes
    Route::middleware(['role:Super Admin|Content Manager'])->group(function () {
        Route::resource('categories', CategoryController::class)
            ->middleware('permission:manage-category');
        
        Route::resource('platforms', PlatformController::class)
            ->middleware('permission:manage-platform');
        
        Route::resource('calendar', EditorialCalendarController::class)
            ->middleware('permission:manage-calendar');

        // Calendar Comments Routes
        Route::post('calendar/{calendar}/comments', [CalendarCommentController::class, 'store'])
            ->name('calendar.comments.store')
            ->middleware('permission:manage-calendar');
        
        Route::delete('calendar/{calendar}/comments/{comment}', [CalendarCommentController::class, 'destroy'])
            ->name('calendar.comments.destroy')
            ->middleware('permission:manage-calendar');
    });

    // Admin Routes
    Route::middleware('role:Super Admin')->prefix('admin')->name('admin.')->group(function () {
        // Users Management
        Route::resource('users', UserController::class)
            ->middleware('permission:manage-users');
        
        Route::put('users/{user}/approve', [UserController::class, 'approve'])
            ->name('users.approve')
            ->middleware('permission:manage-users');
        
        Route::put('users/{user}/reject', [UserController::class, 'reject'])
            ->name('users.reject')
            ->middleware('permission:manage-users');
        
        Route::put('users/{user}/deactivate', [UserController::class, 'deactivate'])
            ->name('users.deactivate')
            ->middleware('permission:manage-users');
        
        Route::put('users/{user}/ban', [UserController::class, 'ban'])
            ->name('users.ban')
            ->middleware('permission:manage-users');
        
        // Roles Management
        Route::resource('roles', RoleController::class)
            ->middleware('permission:manage-roles');
        
        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('settings.index')
            ->middleware('permission:manage-settings');
        
        Route::post('/settings', [SettingsController::class, 'update'])
            ->name('settings.update')
            ->middleware('permission:manage-settings');
    });

    // News Feed Routes
    Route::resource('news-feeds', NewsFeedController::class)
        ->middleware('permission:view-newsfeed')
        ->except(['store', 'update', 'destroy']);
    
    Route::resource('news-feeds', NewsFeedController::class)
        ->middleware('permission:manage-newsfeed')
        ->only(['store', 'update', 'destroy']);

    Route::post('news-feeds/preview', [NewsFeedController::class, 'preview'])
        ->name('news-feeds.preview')
        ->middleware('permission:manage-newsfeed');
    
    Route::post('/news-feeds/fetch-metadata', [NewsFeedController::class, 'fetchMetadata'])
        ->name('news-feeds.fetch-metadata')
        ->middleware('permission:manage-newsfeed');

    // Team Routes
    Route::resource('teams', TeamController::class)
        ->middleware('permission:view-team')
        ->except(['store', 'update', 'destroy']);
    
    Route::resource('teams', TeamController::class)
        ->middleware('permission:manage-team')
        ->only(['store', 'update', 'destroy']);

    Route::get('teams/{team}/available-users', [TeamController::class, 'getAvailableUsers'])
        ->name('teams.available-users')
        ->middleware('permission:manage-team');
    
    Route::post('teams/{team}/members', [TeamController::class, 'addMember'])
        ->name('teams.members.add')
        ->middleware('permission:manage-team');
    
    Route::delete('teams/{team}/members', [TeamController::class, 'removeMember'])
        ->name('teams.members.remove')
        ->middleware('permission:manage-team');
    
    Route::put('teams/{team}/members', [TeamController::class, 'updateMemberRole'])
        ->name('teams.members.update-role')
        ->middleware('permission:manage-team');

    // Media Routes
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Analytics Routes
    Route::get('analytics', [AnalyticsController::class, 'index'])
        ->name('analytics.index')
        ->middleware(['permission:view-analytics']);

    // Social Media Reports Routes
    Route::resource('social-media-reports', \App\Http\Controllers\SocialMediaReportController::class)
        ->middleware('permission:view-social-media-report')
        ->except(['create', 'store', 'edit', 'update', 'destroy', 'show']);

    Route::resource('social-media-reports', \App\Http\Controllers\SocialMediaReportController::class)
        ->middleware('permission:manage-social-media-report')
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
    
    // Export route
    Route::get('social-media-reports/export', [\App\Http\Controllers\SocialMediaReportController::class, 'export'])
        ->name('social-media-reports.export')
        ->middleware(['permission:export-analytics']);

    // Routes untuk Social Media Analytics
    Route::resource('social-platforms', SocialPlatformController::class);

    // Account Routes
    Route::resource('social-accounts', SocialAccountController::class);
    Route::put('social-accounts/{id}/toggle-status', [SocialAccountController::class, 'toggleStatus'])
        ->name('social-accounts.toggle-status');

    // Metric Data Routes
    Route::resource('metric-data', MetricDataController::class)
        ->middleware(['auth', 'verified', 'permission:manage-metric-data']);
    Route::get('metric-report', [MetricDataController::class, 'report'])
        ->name('metric-data.report')
        ->middleware(['auth', 'verified', 'permission:view-metric-data']);

    // Metric Data Import/Export Routes
    Route::post('metric-data/import', [MetricDataController::class, 'import'])
        ->name('metric-data.import')
        ->middleware(['auth', 'verified', 'permission:manage-metric-data']);
    Route::get('metric-data/export', [MetricDataController::class, 'export'])
        ->name('metric-data.export')
        ->middleware(['auth', 'verified', 'permission:export-metric-data']);
    Route::get('metric-data/template', [MetricDataController::class, 'downloadTemplate'])
        ->name('metric-data.template')
        ->middleware(['auth', 'verified', 'permission:manage-metric-data']);

    // Analytics Dashboard
    Route::get('social-analytics', [SocialMediaAnalyticsController::class, 'index'])
        ->name('social-analytics.index')
        ->middleware('permission:view-analytics');

    Route::get('/metric-data/{id}/debug', [MetricDataController::class, 'debugDelete'])->name('metric-data.debug');

    Route::delete('metric-data/{id}/force', [MetricDataController::class, 'forceDestroy'])
        ->name('metric-data.force-destroy')
        ->middleware(['auth', 'verified', 'permission:manage-metric-data']);
});
