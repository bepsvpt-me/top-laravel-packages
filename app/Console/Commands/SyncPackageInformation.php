<?php

namespace App\Console\Commands;

use App\Package;
use Composer\Semver\Semver;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;

class SyncPackageInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:sync:information';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync package information.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $packages = Package::get();

        $this->output->progressStart($packages->count());

        foreach ($packages->chunk(10) as $chunk) {
            $responses = Http::pool(
                fn (Pool $pool) => $chunk->map(
                    fn (Package $package) => $pool
                        ->as($package->name)
                        ->retry(2)
                        ->withUserAgent($this->userAgent)
                        ->get($package->info_uri),
                ),
            );

            collect($responses)
                ->map(function ($response, string $name) use ($packages) {
                    if (!($response instanceof Response)) {
                        dd($response, $name);
                    }
                    if ($response->ok()) {
                        return $response->json('packages.' . $name . '.0');
                    }

                    if ($response->status() === 404) {
                        $packages->firstWhere('name', $name)?->delete();
                    } else {
                        $this->fatal('Failed to sync package information.', [
                            'name' => $name,
                            'status' => $response->status(),
                            'content' => $response->body(),
                        ]);
                    }

                    return null;
                })
                ->filter()
                ->each(function (array $info, string $name) use ($packages) {
                    /** @var Package $package */
                    $package = $packages->firstWhere('name', $name);

                    $requires = $info['require'] ?? [];

                    $laravel = Arr::only($requires, [
                        'laravel/framework',
                        'illuminate/contracts',
                        'illuminate/support',
                    ]);

                    $constraint = Arr::first($laravel);

                    Assert::nullOrStringNotEmpty($constraint);

                    $package->update([
                        'latest_version' => $info['version'],
                        'min_php_version' => $this->minPHP($requires['php'] ?? null),
                        'min_laravel_version' => $this->minLaravel($constraint),
                    ]);
                });

            $this->output->progressAdvance($chunk->count());

            sleep(6);
        }

        $this->output->progressFinish();

        $this->info('Sync package information successfully.');

        return self::SUCCESS;
    }

    /**
     * Get package minimum php required version.
     *
     * @param  string|null  $constraint
     * @return string|null
     */
    protected function minPHP(string $constraint = null): ?string
    {
        static $versions = [];

        if (empty($versions)) {
            $data = [
                '8.3', '8.2', '8.1', '8.0',
                '7.4', '7.3', '7.2', '7.1', '7.0',
                '5.6', '5.5', '5.4', '5.3',
            ];

            $versions = collect($data)
                ->keyBy(fn (string $v) => $v . '.99')
                ->toArray();
        }

        return $this->minVersion($versions, $constraint);
    }

    /**
     * Get package minimum laravel required version.
     *
     * @param  string|null  $constraint
     * @return string|null
     */
    protected function minLaravel(string $constraint = null): ?string
    {
        static $versions = [];

        if (empty($versions)) {
            $data = [
                '9.0', '8.0', '7.0', '6.0',
                '5.8', '5.7', '5.6', '5.5', '5.4', '5.3', '5.2', '5.1', '5.0',
                '4.2', '4.1', '4.0',
            ];

            $versions = collect($data)
                ->keyBy(function (string $v) {
                    return Semver::satisfies($v, '>=6.0')
                        ? Str::replace('.0', '.99', $v)
                        : $v . '.99';
                })
                ->toArray();
        }

        return $this->minVersion($versions, $constraint);
    }

    /**
     * Get minimum satisfied version.
     *
     * @param  array<string, string>  $versions
     * @param  string|null  $constraint
     * @return string|null
     */
    protected function minVersion(array $versions, string $constraint = null): ?string
    {
        if ($constraint === null) {
            return null;
        }

        $key = Arr::last(Semver::satisfiedBy(array_keys($versions), $constraint));

        Assert::nullOrString($key);

        return $versions[$key];
    }
}
