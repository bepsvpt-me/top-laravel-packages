<?php

namespace App\Console\Commands;

use App\Package;
use Exception;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Arr;

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
    protected $description = 'Sync Laravel package list.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $url = '/search.json?tags=laravel&type=library&per_page=100&page=1';

        while (true) {
            $data = $this->fetch($url);

            if (empty($data)) {
                break;
            }

            $this->save($data['results']);

            if (!isset($data['next'])) {
                break;
            }

            $url = urldecode($data['next']);
        }

        $this->info('Laravel package list syncs successfully.');
    }

    /**
     * Fetch remote data.
     *
     * @param string $url
     *
     * @return array<mixed>|null
     */
    protected function fetch(string $url): ?array
    {
        try {
            $response = $this->client->get($url);

            $content = $response->getBody()->getContents();

            return json_decode($content, true);
        } catch (TransferException $e) {
            $this->fatal($e->getMessage(), ['url' => $url]);
        } catch (Exception $e) {
            $this->critical($e->getMessage(), ['url' => $url]);
        }

        return null;
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
                $this->fatal(
                    'Could not create or update package.',
                    $packages
                );
            }
        }
    }
}
