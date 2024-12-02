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
        // Add the scheduled command
        $schedule->command('product-views:delete-old')
                                                                         // ->dailyAt('00:00')
            ->everyMinute()                                                  // ตั้งให้รันทุกนาที                                         // กำหนดเวลารันทุกวันเวลาเที่ยงคืน
            ->appendOutputTo(storage_path('logs/DeleteOldProductViews.log')) // บันทึกผลลัพธ์ลงไฟล์
            ->runInBackground();                                             // รันใน background
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
