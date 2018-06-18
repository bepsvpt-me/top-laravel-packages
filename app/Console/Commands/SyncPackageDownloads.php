<?php

namespace App\Console\Commands;

use App\Download;
use App\Package;
use GuzzleHttp\Promise;
use Illuminate\Database\Eloquent\Collection;
use Log;

class SyncPackageDownloads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:package-downloads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync package downloads.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packages = Package::get(['id', 'url']);

        $packages->chunk(10)->each(function (Collection $packages) {
            $results = Promise\settle($this->asyncUrls($packages))->wait();

            foreach ($packages as $package) {
                $downloads = $this->parsePromiseResponse($package->getKey(), $results);

                foreach ($downloads as $date => $value) {
                    Download::firstOrNew([
                        'package_id' => $package->getKey(),
                        'date' => $date,
                        'type' => 'daily',
                    ])
                        ->fill(['downloads' => $value])
                        ->save();
                }
            }
        });

        $this->info('Command execute successfully.');
    }

    /**
     * Get package downloads api urls.
     *
     * @param Collection $packages
     *
     * @return array
     */
    protected function asyncUrls(Collection $packages): array
    {
        $urls = $packages->mapWithKeys(function (Package $package) {
            $download = $package->downloads()
                ->where('type', 'daily')
                ->orderByDesc('date')
                ->first(['date']);

            $url = sprintf('%s/stats/all.json?average=daily', $package->url);

            if (! is_null($download)) {
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
    protected function parsePromiseResponse(int $key, array $haystack): array
    {
        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $haystack[$key]['value'];

        if (200 !== $response->getStatusCode()) {
            Log::error('failed to fetch package download information', [
                'id' => $key,
            ]);

            return [];
        }

        $content = $response->getBody()->getContents();

        $data = json_decode($content, true);

        return array_combine($data['labels'], $data['values']);
    }
}
