<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            \Log::info('Scheduler Test: Task is running.');
        })->everyMinute();

        // Add the scheduled command
        $schedule->command('product-views:delete-old')
            ->dailyAt('00:00')
        // ->appendOutputTo(storage_path('logs/DeleteOldProductViews.log'))
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
