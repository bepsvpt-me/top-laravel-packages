<?php

namespace App\Console\Commands;

use App\Package;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

final class SyncPackageDownloads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:sync:downloads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync package downloads.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Package::all(['id', 'url'])->chunk(10)->each(function (Collection $packages) {
            $results = Promise\settle($this->urls($packages))->wait();

            /** @var Package $package */

            foreach ($packages as $package) {
                foreach ($this->retrieve($package->getKey(), $results) as $date => $value) {
                    $package->downloads()
                        ->firstOrNew(['date' => $date, 'type' => 'daily'])
                        ->fill(['downloads' => $value])
                        ->save();
                }
            }
        });

        $this->info('Package downloads information sync successfully.');
    }

    /**
     * Get package downloads api urls.
     *
     * @param Collection $packages
     *
     * @return array
     */
    protected function urls(Collection $packages): array
    {
        $urls = $packages->mapWithKeys(function (Package $package) {
            $download = $package->downloads()
                ->where('type', 'daily')
                ->orderByDesc('date')
                ->first(['date']);

            $url = sprintf('%s/stats/all.json?average=daily', $package->url);

            if (!is_null($download)) {
                $url = sprintf('%s&from=%s', $url, $download->date);
            }

            return [$package->getKey() => $this->client->getAsync($url)];
        });

        return $urls->toArray();
    }

    /**
     * Parse promise response and get data.
     *
     * @param int   $key
     * @param array $haystack
     *
     * @return array
     */
    protected function retrieve(int $key, array $haystack): array
    {
        /** @var Response $response */

        $response = $haystack[$key]['value'];

        if (200 !== $response->getStatusCode()) {
            Log::error('[package:sync:downloads] Failed to fetch package downloads information.', [
                'id' => $key,
            ]);

            return [];
        }

        $data = json_decode($response->getBody()->getContents(), true);

        return array_combine($data['labels'], $data['values']);
    }
}
