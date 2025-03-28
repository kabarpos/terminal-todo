<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\CalendarComment;
use App\Models\Category;
use App\Models\EditorialCalendar;
use App\Models\Media;
use App\Models\Metric;
use App\Models\MetricData;
use App\Models\MetricTarget;
use App\Models\NewsFeed;
use App\Models\Platform;
use App\Models\Role;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Models\SocialMediaReport;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Team;
use App\Models\User;
use App\Models\UserMeta;
use App\Observers\ActivityObserver;
use App\Observers\CalendarCommentObserver;
use App\Observers\CategoryObserver;
use App\Observers\EditorialCalendarObserver;
use App\Observers\MediaObserver;
use App\Observers\MetricDataObserver;
use App\Observers\MetricObserver;
use App\Observers\MetricTargetObserver;
use App\Observers\NewsFeedObserver;
use App\Observers\PlatformObserver;
use App\Observers\RoleObserver;
use App\Observers\SettingObserver;
use App\Observers\SocialAccountObserver;
use App\Observers\SocialMediaReportObserver;
use App\Observers\TaskCommentObserver;
use App\Observers\TaskObserver;
use App\Observers\TeamObserver;
use App\Observers\UserMetaObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All observer models to be registered
     */
    protected $observers = [
        \App\Models\Task::class => \App\Observers\TaskObserver::class,
        \App\Models\User::class => \App\Observers\UserObserver::class,
        \App\Models\Team::class => \App\Observers\TeamObserver::class,
        \App\Models\Category::class => \App\Observers\CategoryObserver::class,
        \App\Models\Platform::class => \App\Observers\PlatformObserver::class,
        \App\Models\TaskComment::class => \App\Observers\TaskCommentObserver::class,
        \App\Models\EditorialCalendar::class => \App\Observers\EditorialCalendarObserver::class,
        \App\Models\CalendarComment::class => \App\Observers\CalendarCommentObserver::class,
        \App\Models\NewsFeed::class => \App\Observers\NewsFeedObserver::class,
        \App\Models\Media::class => \App\Observers\MediaObserver::class,
        \App\Models\Role::class => \App\Observers\RoleObserver::class,
        \App\Models\Setting::class => \App\Observers\SettingObserver::class,
        \App\Models\UserMeta::class => \App\Observers\UserMetaObserver::class,
        \App\Models\Activity::class => \App\Observers\ActivityObserver::class,
        \App\Models\Metric::class => \App\Observers\MetricObserver::class,
        \App\Models\MetricData::class => \App\Observers\MetricDataObserver::class,
        \App\Models\MetricTarget::class => \App\Observers\MetricTargetObserver::class,
        \App\Models\SocialAccount::class => \App\Observers\SocialAccountObserver::class,
        \App\Models\SocialMediaReport::class => \App\Observers\SocialMediaReportObserver::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        
        // Register all observers
        foreach ($this->observers as $model => $observer) {
            if (class_exists($model) && class_exists($observer)) {
                $model::observe($observer);
            }
        }
        
        // Monitor queue jobs in production untuk logging dan monitoring
        if (app()->environment('production')) {
            Queue::before(function (JobProcessing $event) {
                Log::channel('queue')->info('Processing job', [
                    'id' => $event->job->getJobId(),
                    'name' => $event->job->resolveName(),
                    'queue' => $event->job->getQueue(),
                    'attempts' => $event->job->attempts(),
                    'payload' => json_decode($event->job->getRawBody(), true),
                ]);
            });
            
            Queue::after(function (JobProcessed $event) {
                Log::channel('queue')->info('Job processed', [
                    'id' => $event->job->getJobId(),
                    'name' => $event->job->resolveName(),
                    'queue' => $event->job->getQueue(),
                    'time' => $event->job->timeoutAt(),
                ]);
            });
        }
    }
}
