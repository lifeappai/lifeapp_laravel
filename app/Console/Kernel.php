<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('expire:quiz')->everyFiveMinutes();

        // $schedule->command('export:users')->dailyAt("00:05");

        $schedule->command('schedule:push-notification-campaigns')->everyFiveMinutes();

        $schedule->command('set:user-rank')->hourly();

        $schedule->command('calculate:teacher-tscores')->dailyAt('12:15');

        $schedule->command('calculate:school-sscores')->dailyAt('12:30');
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
}
