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
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\MetricDataController;
use App\Http\Controllers\SocialMediaAnalyticsController;
use App\Http\Controllers\SocialMediaReportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Routes
Route::get('/', function () {
    return Inertia::render('Public/Home');
})->name('home');

// Auth Routes (generated by Breeze)
require __DIR__.'/auth.php';

// Protected Routes
Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureUserIsActive::class])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Index');
    })->middleware('throttle:dashboard')->name('dashboard');

    // Tasks Routes
    Route::resource('tasks', TaskController::class)
        ->middleware(['permission:view-task|manage-task', 'throttle:tasks']);

    Route::put('tasks/{id}/update-status', [TaskController::class, 'updateStatus'])
        ->name('tasks.update-status')
        ->middleware(['permission:manage-task', 'throttle:tasks']);
    
    // Task Comments Routes
    Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store'])
        ->name('tasks.comments.store')
        ->middleware(['permission:manage-task', 'throttle:comments']);
    
    Route::put('tasks/comments/{comment}', [TaskCommentController::class, 'update'])
        ->name('tasks.comments.update')
        ->middleware(['permission:manage-task', 'throttle:comments']);
    
    Route::delete('tasks/comments/{comment}', [TaskCommentController::class, 'destroy'])
        ->name('tasks.comments.destroy')
        ->middleware('permission:manage-task');

    // Profile Routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Content Management Routes
    Route::middleware(['auth'])->group(function () {
        Route::resource('categories', CategoryController::class)
            ->middleware('permission:view-category|manage-category');
        
        Route::resource('platforms', PlatformController::class)
            ->middleware('permission:view-platform|manage-platform');
        
        Route::resource('calendar', EditorialCalendarController::class)
            ->middleware('permission:view-calendar|manage-calendar');

        // Calendar Comments Routes
        Route::post('calendar/{calendar}/comments', [CalendarCommentController::class, 'store'])
            ->name('calendar.comments.store')
            ->middleware(['permission:manage-calendar', 'throttle:comments']);
        
        Route::delete('calendar/{calendar}/comments/{comment}', [CalendarCommentController::class, 'destroy'])
            ->name('calendar.comments.destroy')
            ->middleware('permission:manage-calendar');
    });

    // Admin Routes
    Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
        // Users Management
        Route::resource('users', UserController::class)
            ->middleware('permission:view-users|manage-users');
        
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
            ->middleware('permission:view-roles|manage-roles');
        
        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('settings.index')
            ->middleware('permission:view-settings|manage-settings');
        
        Route::post('/settings', [SettingsController::class, 'update'])
            ->name('settings.update')
            ->middleware('permission:manage-settings');
    });

    // News Feed Routes
    Route::get('news-feeds', [NewsFeedController::class, 'index'])
        ->name('news-feeds.index')
        ->middleware('permission:view-newsfeed');
        
    // News Feed Management Routes (dengan middleware manage-newsfeed)
    Route::get('news-feeds/create', [NewsFeedController::class, 'create'])
        ->name('news-feeds.create')
        ->middleware('permission:manage-newsfeed');
        
    Route::get('news-feeds/{news_feed}', [NewsFeedController::class, 'show'])
        ->name('news-feeds.show')
        ->middleware('permission:view-newsfeed');
        
    Route::post('news-feeds', [NewsFeedController::class, 'store'])
        ->name('news-feeds.store')
        ->middleware(['permission:manage-newsfeed', 'throttle:tasks']);
        
    Route::get('news-feeds/{news_feed}/edit', [NewsFeedController::class, 'edit'])
        ->name('news-feeds.edit')
        ->middleware('permission:manage-newsfeed');
        
    Route::put('news-feeds/{news_feed}', [NewsFeedController::class, 'update'])
        ->name('news-feeds.update')
        ->middleware(['permission:manage-newsfeed', 'throttle:tasks']);
        
    Route::delete('news-feeds/{news_feed}', [NewsFeedController::class, 'destroy'])
        ->name('news-feeds.destroy')
        ->middleware('permission:manage-newsfeed');

    Route::post('news-feeds/preview', [NewsFeedController::class, 'preview'])
        ->name('news-feeds.preview')
        ->middleware(['permission:manage-newsfeed', 'throttle:tasks']);
    
    Route::post('/news-feeds/fetch-metadata', [NewsFeedController::class, 'fetchMetadata'])
        ->name('news-feeds.fetch-metadata')
        ->middleware(['permission:manage-newsfeed', 'throttle:tasks']);

    // Team Routes
    Route::get('teams', [TeamController::class, 'index'])
        ->name('teams.index')
        ->middleware('permission:view-team');
        
    // Team Management Routes
    Route::get('teams/create', [TeamController::class, 'create'])
        ->name('teams.create')
        ->middleware('permission:manage-team');
        
    Route::get('teams/{team}', [TeamController::class, 'show'])
        ->name('teams.show')
        ->middleware('permission:view-team');
        
    Route::post('teams', [TeamController::class, 'store'])
        ->name('teams.store')
        ->middleware('permission:manage-team');
        
    Route::get('teams/{team}/edit', [TeamController::class, 'edit'])
        ->name('teams.edit')
        ->middleware('permission:manage-team');
        
    Route::put('teams/{team}', [TeamController::class, 'update'])
        ->name('teams.update')
        ->middleware('permission:manage-team');
        
    Route::delete('teams/{team}', [TeamController::class, 'destroy'])
        ->name('teams.destroy')
        ->middleware('permission:manage-team');

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
    Route::get('media', [MediaController::class, 'index'])
        ->name('media.index')
        ->middleware('permission:view-media');
    Route::post('media', [MediaController::class, 'store'])
        ->name('media.store')
        ->middleware(['permission:manage-media', 'throttle:uploads']);
    Route::delete('media/{media}', [MediaController::class, 'destroy'])
        ->name('media.destroy')
        ->middleware('permission:manage-media');
    // Media Trash Routes
    Route::get('media/trash', [MediaController::class, 'trash'])
        ->name('media.trash')
        ->middleware('permission:manage-media');
    Route::post('media/restore/{id}', [MediaController::class, 'restore'])
        ->name('media.restore')
        ->middleware('permission:manage-media');
    Route::delete('media/force-delete/{id}', [MediaController::class, 'forceDelete'])
        ->name('media.force-delete')
        ->middleware('permission:manage-media');

    // Analytics Routes
    Route::get('analytics', [AnalyticsController::class, 'index'])
        ->name('analytics.index')
        ->middleware(['permission:view-analytics']);

    // Social Media Reports Routes
    Route::resource('social-media-reports', SocialMediaReportController::class)
        ->middleware('permission:view-social-media-report|manage-social-media-report');
    
    // Social Accounts Routes
    Route::resource('social-accounts', SocialAccountController::class)
        ->middleware('permission:view-social-account|manage-social-account');
    Route::put('social-accounts/{id}/toggle-status', [SocialAccountController::class, 'toggleStatus'])
        ->name('social-accounts.toggle-status')
        ->middleware('permission:manage-social-account');
    
    // Metric Data Import/Export Routes - PENTING: rutenya diletakkan SEBELUM resource route
    Route::get('metric-data/template', [MetricDataController::class, 'downloadTemplate'])
        ->name('metric-data.template')
        ->middleware(['auth', 'verified'])
        ->withoutMiddleware(['verify_csrf_token']);
        
    Route::get('metric-data/export', [MetricDataController::class, 'export'])
        ->name('metric-data.export')
        ->middleware(['auth', 'verified'])
        ->withoutMiddleware(['verify_csrf_token']);
        
    Route::post('metric-data/import', [MetricDataController::class, 'import'])
        ->name('metric-data.import')
        ->middleware(['auth', 'verified']);

    // Metric Data Routes
    Route::resource('metric-data', MetricDataController::class)
        ->middleware(['auth', 'verified', 'permission:manage-metric-data|view-metric-data']);
        
    Route::get('metric-report', [MetricDataController::class, 'report'])
        ->name('metric-data.report')
        ->middleware(['auth', 'verified', 'permission:view-metric-data']);

    // Analytics Dashboard
    Route::get('social-analytics', [SocialMediaAnalyticsController::class, 'index'])
        ->name('social-analytics.index')
        ->middleware('permission:view-analytics');

    Route::get('/metric-data/{id}/debug', [MetricDataController::class, 'debugDelete'])->name('metric-data.debug');

    Route::delete('metric-data/{id}/force', [MetricDataController::class, 'forceDestroy'])
        ->name('metric-data.force-destroy')
        ->middleware(['auth', 'verified', 'permission:manage-metric-data']);
});
