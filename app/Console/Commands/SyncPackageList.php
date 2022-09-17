<?php

namespace App\Console\Commands;

use App\Package;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SyncPackageList extends Command
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
     * @var array<int, string>
     */
    protected array $fields = [
        'description',
        'url',
        'repository',
        'downloads',
        'favers',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $responses = Http::pool(
            fn (Pool $pool) => $this->uris()->map(
                fn (string $uri) => $pool
                    ->as($uri)
                    ->retry(2)
                    ->withUserAgent($this->userAgent)
                    ->get($uri),
            ),
        );

        collect($responses)
            ->map(function (Response $response, string $uri) {
                if ($response->ok()) {
                    return $response->json('results');
                }

                $this->fatal('Failed to fetch search result.', [
                    'uri' => $uri,
                    'status' => $response->status(),
                    'content' => $response->body(),
                ]);

                return null;
            })
            ->filter()
            ->collapse()
            ->each(function (array $data) {
                $attributes = collect($data)
                    ->only($this->fields)
                    ->put('deleted_at', null)
                    ->toArray();

                Package::query()
                       ->withTrashed()
                       ->updateOrCreate(
                           ['name' => $data['name']],
                           $attributes,
                       );
            });

        $this->info('Sync Laravel package list successfully.');

        return self::SUCCESS;
    }

    /**
     * Get all search URIs.
     *
     * @return Collection<int, string>
     */
    protected function uris(): Collection
    {
        $pages = range(1, 10);

        return collect($pages)->map(function (int $page) {
            $queries = http_build_query([
                'tags' => 'laravel',
                'type' => 'library',
                'per_page' => 100,
                'page' => $page,
            ]);

            return 'https://packagist.org/search.json?' . $queries;
        });
    }
}
