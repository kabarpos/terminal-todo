<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
    protected function schedule(Schedule $schedule)
    {
        // Jalankan pengambilan data metrik setiap hari jam 00:00
        $schedule->command('metrics:fetch')
            ->dailyAt('00:00')
            ->appendOutputTo(storage_path('logs/metrics-fetch.log'));

        // Membersihkan media yang di-softdelete lebih dari 30 hari
        $schedule->command('media:clean 30')->daily()->at('01:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        // ... other commands ...
        \App\Console\Commands\DebugPermissions::class,
    ];
} 