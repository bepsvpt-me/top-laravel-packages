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
                $downloads = $this->getPackageDownloads($package->getKey(), $type);

                $groupOfDays = $downloads->groupBy(function (Download $download) use ($type) {
                    $method = sprintf('startOf%s', ucfirst(substr($type, 0, -2)));

                    return Carbon::parse($download->date)->{$method}()->toDateString();
                });

                $groupOfDays->each(function (Collection $downloads, $date) use ($type) {
                    Download::firstOrNew([
                        'package_id' => $downloads->first()->package_id,
                        'date' => $date,
                        'type' => $type,
                    ])
                        ->fill(['downloads' => $downloads->sum('downloads')])
                        ->save();
                });
            }
        });
    }

    /**
     * Get package downloads information.
     *
     * @param int    $packageId
     * @param string $type
     *
     * @return Collection
     */
    protected function getPackageDownloads(int $packageId, string $type): Collection
    {
        $date = $this->getPackageNewestDownloadDate($packageId, $type);

        $downloads = Download::where('package_id', $packageId)
            ->where('type', 'daily');

        if (! is_null($date)) {
            $downloads = $downloads->where('date', '>=', $date);
        }

        return $downloads->get();
    }

    /**
     * Get latest download information.
     *
     * @param int    $packageId
     * @param string $type
     *
     * @return null|string
     */
    protected function getPackageNewestDownloadDate(int $packageId, string $type): ?string
    {
        $download = Download::where('package_id', $packageId)
            ->where('type', $type)
            ->latest('date')
            ->first();

        if (is_null($download)) {
            return null;
        }

        return $download->date;
    }
}
