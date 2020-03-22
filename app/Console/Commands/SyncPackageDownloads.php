<?php

namespace App\Console\Commands;

use App\Package;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Collection;
use Throwable;
use function GuzzleHttp\Promise\unwrap;

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
        $chunks = $this->packages();

        $this->output->progressStart($chunks->sum->count());

        /** @var Collection $packages */

        foreach ($chunks as $packages) {
            try {
                $responses = $this->responses($packages);
            } catch (Throwable $e) {
                $this->fatal(
                    'Failed to sync package downloads.',
                    $packages->pluck('id')->toArray()
                );

                continue;
            }

            foreach ($responses as $key => $content) {
                /** @var Package $package */

                $package = $packages->where('id', $key)->first();

                /** @var array<int> $downloads */

                $downloads = array_combine(
                    $content['labels'],
                    $content['values'][$package->name]
                );

                $this->save($package, $downloads);
            }

            $this->output->progressAdvance($packages->count());

            sleep(mt_rand(2, 5));
        }

        $this->output->progressFinish();

        $this->info('Package downloads information syncs successfully.');
    }

    /**
     * Get package chunks.
     *
     * @return Collection
     */
    protected function packages(): Collection
    {
        return Package::query()
            ->get(['id', 'name'])
            ->chunk(5);
    }

    /**
     * Get api responses.
     *
     * @param Collection|Package[] $packages
     *
     * @return array<mixed>
     *
     * @throws Throwable
     */
    protected function responses(Collection $packages): array
    {
        $promises = [];

        foreach ($packages as $package) {
            $promises[$package->getKey()] =
                $this->client->getAsync($this->url($package));
        }

        return array_map(function (Response $response) {
            return json_decode(
                $response->getBody()->getContents(),
                true
            );
        }, unwrap($promises));
    }

    /**
     * Get package downloads info api url.
     *
     * @param Package $package
     *
     * @return string
     */
    protected function url(Package $package): string
    {
        return sprintf(
            '/packages/%s/stats/all.json?average=daily&from=%s',
            $package->name,
            $package->syncedAt()
        );
    }

    /**
     * Save package download info.
     *
     * @param Package $package
     * @param array<int> $downloads
     *
     * @return void
     */
    protected function save(Package $package, array $downloads): void
    {
        foreach ($downloads as $date => $value) {
            $package->downloads()
                ->firstOrNew(['date' => $date, 'type' => 'daily'])
                ->fill(['downloads' => $value])
                ->save();
        }
    }
}
