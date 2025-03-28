<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These commands will be run in a single, sequential run.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Membersihkan log setiap minggu
        $schedule->command('log:clear')
            ->weekly()
            ->appendOutputTo(storage_path('logs/scheduler.log'));
        
        // Membersihkan cache Redis setiap hari
        $schedule->command('cache:prune-stale-tags')
            ->daily()
            ->onOneServer()
            ->environments(['production', 'staging'])
            ->appendOutputTo(storage_path('logs/scheduler.log'));
        
        // Retry failed jobs setiap jam
        $schedule->command('queue:retry all')
            ->hourly()
            ->onOneServer()
            ->environments(['production', 'staging'])
            ->appendOutputTo(storage_path('logs/scheduler.log'));
        
        // Hapus media yang tidak terpakai setiap minggu
        $schedule->command('media:prune')
            ->weekly()
            ->onOneServer()
            ->environments(['production', 'staging'])
            ->appendOutputTo(storage_path('logs/scheduler.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        // ... other commands ...
        \App\Console\Commands\DebugPermissions::class,
    ];
} 