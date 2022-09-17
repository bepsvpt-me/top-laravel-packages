<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync packages.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $jobs = [
            SyncPackageList::class,
            SyncPackageDownloads::class,
            SyncPackageInformation::class,
            CalculateDownloads::class,
            CalculateWeights::class,
        ];

        foreach ($jobs as $job) {
            $this->call($job);

            sleep(10);
        }

        return self::SUCCESS;
    }
}
