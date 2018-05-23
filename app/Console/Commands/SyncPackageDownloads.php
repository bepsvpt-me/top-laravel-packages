<?php

namespace App\Console\Commands;

use App\Download;
use App\Package;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

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
    protected $description = 'Command description';

    /**
     * @var Client
     */
    protected $client;

    /**
     * Create a new command instance.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct();

        $this->client = $client;
    }

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
                $response = $results[$package->getKey()]['value'];

                $content = json_decode($response->getBody()->getContents(), true);

                $downloads = array_combine($content['labels'], $content['values']);

                foreach ($downloads as $date => $value) {
                    $model = Download::firstOrNew([
                        'package_id' => $package->getKey(),
                        'date' => $date,
                        'type' => 'daily',
                    ]);

                    if ($model->exists) {
                        $model->update(['downloads' => $value]);
                    } else {
                        $package->downloads()->save($model->fill(['downloads' => $value]));
                    }
                }
            }
        });

        $this->info('Sync package downloads successfully.');
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
}
