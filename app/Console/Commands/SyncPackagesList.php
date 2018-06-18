<?php

namespace App\Console\Commands;

use App\Package;
use Log;

class SyncPackagesList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:packages-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync laravel packages list.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = 'https://packagist.org/search.json?tags=laravel&type=library&per_page=100&page=1';

        while (true) {
            if (empty($data = $this->fetch($url))) {
                break;
            }

            $this->saveToDatabase($data['results']);

            if (! isset($data['next'])) {
                break;
            }

            $url = urldecode($data['next']);
        }

        $this->info('Command execute successfully.');
    }

    /**
     * Fetch package search data.
     *
     * @param string $url
     *
     * @return array
     */
    protected function fetch(string $url): array
    {
        $response = $this->client->get($url, ['http_errors' => false]);

        if (200 !== $response->getStatusCode()) {
            Log::error('failed to sync package list');

            return [];
        }

        $content = $response->getBody()->getContents();

        return json_decode($content, true);
    }

    /**
     * Save packages information to database.
     *
     * @param array $packages
     *
     * @return void
     */
    protected function saveToDatabase(array $packages): void
    {
        foreach ($packages as $package) {
            $model = Package::updateOrCreate(['name' => $package['name']],
                array_only($package, [
                    'name', 'description', 'url', 'repository',
                    'downloads', 'favers',
                ])
            );

            if ($model->isDirty()) {
                Log::error('could not create or update package', [
                    'name' => $package['name'],
                ]);
            }
        }
    }
}
