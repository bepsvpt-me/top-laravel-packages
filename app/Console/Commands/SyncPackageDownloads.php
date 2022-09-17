<?php

namespace App\Console\Commands;

use App\Package;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

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
     * @return int
     */
    public function handle(): int
    {
        $packages = Package::get(['id', 'name']);

        $this->output->progressStart($packages->count());

        foreach ($packages->chunk(10) as $chunk) {
            $responses = Http::pool(
                fn (Pool $pool) => $chunk->map(
                    fn (Package $package) => $pool
                        ->as($package->name)
                        ->retry(2)
                        ->withUserAgent($this->userAgent)
                        ->get($package->stats_uri),
                ),
            );

            collect($responses)
                ->map(function (Response $response, string $name) use ($packages) {
                    if ($response->ok()) {
                        $keys = $response->json('labels');

                        $values = $response->json('values.' . $name);

                        if (!is_array($keys) || !is_array($values)) {
                            return null;
                        }

                        return array_combine($keys, $values);
                    }

                    if ($response->status() === 404) {
                        $packages->firstWhere('name', $name)?->delete();
                    } else {
                        $this->fatal('Failed to sync package downloads.', [
                            'name' => $name,
                            'status' => $response->status(),
                            'content' => $response->body(),
                        ]);
                    }

                    return null;
                })
                ->filter()
                ->each(function (array $records, string $name) use ($packages) {
                    /** @var Package $package */
                    $package = $packages->firstWhere('name', $name);

                    foreach ($records as $date => $downloads) {
                        $package->downloads()->updateOrCreate([
                            'date' => $date,
                            'type' => 'daily',
                        ], [
                            'downloads' => $downloads,
                        ]);
                    }
                });

            $this->output->progressAdvance($chunk->count());

            sleep(6);
        }

        $this->output->progressFinish();

        $this->info('Package downloads information syncs successfully.');

        return self::SUCCESS;
    }
}
