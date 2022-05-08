<?php

namespace App\Console\Commands;

use App\Package;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Collection;
use Throwable;
use Webmozart\Assert\Assert;

/**
 * @template TDownload of array {
 *     labels: array<int, string>,
 *     values: array<string, array<int, int>>,
 *     average: string
 * }
 */
class SyncPackageDownloads extends Command
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
        $this->output->progressStart(Package::count());

        Package::select(['id', 'name'])->chunk(5, function (Collection $packages) {
            /** @var Collection<int, Package> $packages */

            try {
                $responses = $this->responses($packages);
            } catch (Throwable $e) {
                $this->fatal(
                    'Failed to sync package downloads.',
                    $packages->pluck('id')->toArray()
                );

                return;
            }

            foreach ($responses as $key => $content) {
                $package = $packages->where('id', $key)->first();

                Assert::isInstanceOf($package, Package::class);

                $downloads = array_combine(
                    $content['labels'],
                    $content['values'][$package->name]
                );

                $this->save($package, $downloads);
            }

            $this->output->progressAdvance($packages->count());

            sleep(mt_rand(2, 5));
        });

        $this->output->progressFinish();

        $this->info('Package downloads information syncs successfully.');
    }

    /**
     * Get api responses.
     *
     * @param Collection<int, Package> $packages
     *
     * @return TDownload
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
            /** @var TDownload $data */

            $data = json_decode(
                $response->getBody()->getContents(),
                true
            );

            return $data;
        }, Utils::unwrap($promises));
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
     * @param array<string, int> $downloads
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
