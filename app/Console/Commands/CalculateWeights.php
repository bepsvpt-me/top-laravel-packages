<?php

namespace App\Console\Commands;

use App\Package;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class CalculateWeights extends Command
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
     * @return int
     */
    public function handle(): int
    {
        $chunks = Package::orderByDesc('downloads')
            ->get(['id', 'downloads'])
            ->split(Package::TOTAL_WEIGHTS);

        $weight = 1;

        /** @var Collection<int, Package> $packages */
        foreach ($chunks as $packages) {
            $ids = $packages->pluck('id')->toArray();

            Package::whereIn('id', $ids)->update(['weights' => $weight++]);
        }

        $this->info('Package weights calculate successfully.');

        return self::SUCCESS;
    }
}
