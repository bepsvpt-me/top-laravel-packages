<?php

namespace App\Console\Commands;

use App\Package;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

final class CalculateWeights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:calc:weights';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate package weights according to its downloads.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $chunks = Package::query()
            ->orderByDesc('downloads')
            ->get(['id', 'downloads'])
            ->split(Package::TOTAL_WEIGHTS);

        $weight = 1;

        /** @var Collection $packages */

        foreach ($chunks as $packages) {
            Package::query()
                ->whereIn('id', $packages->pluck('id')->toArray())
                ->update(['weights' => $weight++]);
        }

        $this->info('Package weights calculate successfully.');
    }
}
