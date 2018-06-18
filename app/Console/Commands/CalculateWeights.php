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
    protected $signature = 'calc:weight';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate package weights according to its downloads.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packages = $this->packages();

        $weightBoundaries = $this->weightBoundaries($packages->count());

        $weightBoundary = array_pop($weightBoundaries);

        $weight = 1;

        foreach ($packages as $index => $package) {
            if ($index > $weightBoundary) {
                ++$weight;

                $weightBoundary = array_pop($weightBoundaries);
            }

            $package->update(['weights' => $weight]);
        }

        $this->info('Command execute successfully.');
    }

    /**
     * Get packages downloads information.
     *
     * @return Collection
     */
    protected function packages(): Collection
    {
        return Package::orderByDesc('downloads')
            ->get(['id', 'downloads']);
    }

    /**
     * Get packages weights boundaries.
     *
     * @param $total
     *
     * @return array
     */
    protected function weightBoundaries($total): array
    {
        $gap = intval(ceil($total / Package::TOTAL_WEIGHTS));

        // use reverse oder in order to use array_pop instead of array_shift
        return array_map(function ($weight) use ($gap) {
            return $weight * $gap;
        }, range(Package::TOTAL_WEIGHTS, 1));
    }
}
