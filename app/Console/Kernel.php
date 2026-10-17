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
        $tz = config('app.timezone', 'Asia/Karachi');

        $schedule->command('luxsecure:backup')->dailyAt('02:00')->timezone($tz);
        $schedule->command('luxsecure:notify-rent-due')->dailyAt('08:00')->timezone($tz);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
