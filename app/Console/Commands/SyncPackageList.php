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
        $url = 'https://packagist.org/search.json?tags=laravel&type=library&per_page=100&page=1';

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

        $this->info('Laravel packages list sync successfully.');
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

        if (200 === $response->getStatusCode()) {
            return json_decode($response->getBody()->getContents(), true);
        }

        Log::error('[package:sync:list] Failed to sync package list.');

        return [];
    }

    /**
     * Save packages information to database.
     *
     * @param array $packages
     *
     * @return void
     */
    protected function save(array $packages): void
    {
        $fields = ['name', 'description', 'url', 'repository', 'downloads', 'favers'];

        foreach ($packages as $package) {
            $model = Package::query()->updateOrCreate(
                ['name' => $package['name']],
                Arr::only($package, $fields),
            );

            if ($model->isDirty()) {
                Log::error('[package:sync:list] Could not create or update package.', [
                    'name' => $package['name'],
                ]);
            }
        }
    }
}
