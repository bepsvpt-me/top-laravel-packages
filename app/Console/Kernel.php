<?php

namespace App\Console;

use App\Console\Commands\CalculateDownloads;
use App\Console\Commands\CalculateWeights;
use App\Console\Commands\SyncPackageDownloads;
use App\Console\Commands\SyncPackageInformation;
use App\Console\Commands\SyncPackageList;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(SyncPackageList::class)->dailyAt('02:00');
        $schedule->command(SyncPackageDownloads::class)->dailyAt('03:00');
        $schedule->command(SyncPackageInformation::class)->dailyAt('04:00');
        $schedule->command(CalculateDownloads::class)->dailyAt('05:00');
        $schedule->command(CalculateWeights::class)->dailyAt('06:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
