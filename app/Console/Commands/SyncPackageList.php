<?php

namespace App\Console\Commands;

use App\Package;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

final class SyncPackageList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:sync:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync laravel package list.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $url = 'https://packagist.org/search.json?tags[0]=laravel&type=library&per_page=100&page=1';

        while (true) {
            if (empty($data = $this->fetch($url))) {
                break;
            }

            $this->save($data['results']);

            if (!isset($data['next'])) {
                break;
            }

            $url = urldecode($data['next']);
        }

        $this->info('Laravel packages list syncs successfully.');
    }

    /**
     * Fetch package search data.
     *
     * @param string $url
     *
     * @return array<mixed>
     */
    protected function fetch(string $url): array
    {
        $response = $this->client->get($url, [
            'http_errors' => false,
        ]);

        if (200 !== $response->getStatusCode()) {
            Log::error('[package:sync:list] Sync package list failed.', [
                'url' => $url,
                'time' => time(),
            ]);

            return [];
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Save packages information to database.
     *
     * @param array<array<string|int>> $packages
     *
     * @return void
     */
    protected function save(array $packages): void
    {
        $fields = ['description', 'url', 'repository', 'downloads', 'favers'];

        foreach ($packages as $package) {
            $model = Package::query()->updateOrCreate(
                ['name' => $package['name']],
                Arr::only($package, $fields)
            );

            if ($model->isDirty()) {
                Log::error('[package:sync:list] Could not create or update package.', [
                    'name' => $package['name'],
                    'data' => $packages,
                ]);
            }
        }
    }
}
