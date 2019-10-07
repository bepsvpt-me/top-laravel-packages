<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('package:sync:list')->dailyAt('02:01');
        $schedule->command('package:sync:downloads')->dailyAt('03:01');
        $schedule->command('package:sync:information')->dailyAt('04:01');
        $schedule->command('package:calc:downloads')->dailyAt('05:01');
        $schedule->command('package:calc:weights')->dailyAt('05:01');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
