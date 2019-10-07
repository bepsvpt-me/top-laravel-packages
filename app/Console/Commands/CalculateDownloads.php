<?php

namespace App\Console\Commands;

use App\Download;
use App\Package;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

final class CalculateDownloads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:calc:downloads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate package weekly, monthly and yearly downloads.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Package::all(['id'])->each(function (Package $package) {
            foreach (['weekly', 'monthly', 'yearly'] as $type) {
                $this->downloads($package, $type)
                    ->groupBy(function (Download $download) use ($type) {
                        return Carbon::parse($download->date)
                            ->{sprintf('startOf%s', ucfirst(substr($type, 0, -2)))}()
                            ->toDateString();
                    })
                    ->each(function (Collection $downloads, $date) use ($package, $type) {
                        $package->downloads()
                            ->firstOrNew(['date' => $date, 'type' => $type])
                            ->fill(['downloads' => $downloads->sum('downloads')])
                            ->save();
                    });
            }
        });
    }

    /**
     * Get package downloads.
     *
     * @param Package $package
     * @param string $type
     *
     * @return Collection
     */
    protected function downloads(Package $package, string $type): Collection
    {
        $download = $package->downloads()
            ->where('type', $type)
            ->latest('date')
            ->first();

        $downloads = $package->downloads()
            ->where('type', '=', 'daily');

        if (!is_null($download)) {
            $downloads->where('date', '>=', $download->date);
        }

        return $downloads->get();
    }
}
