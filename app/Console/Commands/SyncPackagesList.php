<?php

namespace App\Console\Commands;

use App\Package;
use Artisan;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

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
        $url = 'https://packagist.org/search.json?tags=laravel&type=library&per_page=100&page=1';

        while (true) {
            $response = $this->client->get($url);

            $content = $response->getBody()->getContents();

            $data = json_decode($content, true);

            foreach ($data['results'] as $package) {
                Package::updateOrCreate(['name' => $package['name']],
                    array_only($package, [
                        'name', 'description', 'url', 'repository',
                        'downloads', 'favers',
                    ])
                );
            }

            Artisan::queue('sync:package', ['package' => array_pluck($data['results'], 'name')]);

            if (! isset($data['next'])) {
                break;
            }

            $url = urldecode($data['next']);
        }

        $this->info('Packages sync successfully.');
    }
}
