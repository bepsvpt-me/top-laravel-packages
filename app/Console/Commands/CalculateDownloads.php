<?php

namespace App\Console\Commands;

use App\Download;
use App\Package;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class CalculateDownloads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:calc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate package weekly, monthly and yearly downloads.';

    /**
     * The ranking supports types.
     *
     * @var array
     */
    protected $types = [
        'weekly',
        'monthly',
        'yearly',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Package::all(['id'])->each(function (Package $package) {
            foreach ($this->types as $type) {
                $latest = Download::where('package_id', $package->getKey())
                    ->where('type', $type)
                    ->latest('date')
                    ->first();

                $downloads = Download::where('package_id', $package->getKey())
                    ->where('type', 'daily');

                if (! is_null($latest)) {
                    $downloads = $downloads->where('date', '>=', $latest->date);
                }

                $groupOfDays = $downloads->get()->groupBy(function (Download $download) use ($type) {
                    $method = sprintf('startOf%s', ucfirst(substr($type, 0, -2)));

                    return Carbon::parse($download->date)->{$method}()->toDateString();
                });

                $groupOfDays->each(function (Collection $downloads, $week) use ($type) {
                    Download::firstOrNew([
                        'package_id' => $downloads->first()->package_id,
                        'date' => $week,
                        'type' => $type,
                    ])
                        ->fill(['downloads' => $downloads->sum('downloads')])
                        ->save();
                });
            }
        });
    }
}
