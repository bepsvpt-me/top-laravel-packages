<?php

namespace App\Console\Commands;

use App\Package;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

final class SyncPackageInformation extends Command
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
     * @return void
     */
    public function handle()
    {
        $packages = $this->packages();

        $packages->chunk(10)->each(function (Collection $packages) {
            $results = Promise\settle($this->urls($packages))->wait();

            foreach ($packages as $package) {
                if (empty($data = $this->retrieve($package->getKey(), $results))) {
                    continue;
                }

                $version = $this->latestVersion($data['versions']);

                $requires = $data['versions'][$version]['require'] ?? [];

                $package->update([
                    'dependents' => $data['dependents'],
                    'github_stars' => $data['github_stars'],
                    'github_watchers' => $data['github_watchers'],
                    'github_forks' => $data['github_forks'],
                    'github_open_issues' => $data['github_open_issues'],
                    'latest_version' => $version,
                    'min_php_version' => $this->minPhpVersion($requires['php'] ?? null),
                    'min_laravel_version' => $this->minLaravelVersion($requires['laravel/framework'] ?? $requires['illuminate/support'] ?? null),
                ]);
            }

            sleep(5);
        });

        $this->info('Package information sync successfully.');
    }

    /**
     * Get packages filter by weights.
     *
     * @return Collection
     */
    protected function packages(): Collection
    {
        $day = now()->dayOfYear;

        $weights = array_filter(range(1, Package::TOTAL_WEIGHTS), function ($weight) use ($day) {
            return 0 === ($day % $weight);
        });

        return Package::query()->whereIn('weights', $weights)->get(['id', 'url']);
    }

    /**
     * Get package information api urls.
     *
     * @param Collection $packages
     *
     * @return array
     */
    protected function urls(Collection $packages): array
    {
        $urls = $packages->mapWithKeys(function (Package $package) {
            return [$package->getKey() => $this->client->getAsync(sprintf('%s.json', $package->url))];
        });

        return $urls->toArray();
    }

    /**
     * Parse promise response and get data.
     *
     * @param int   $key
     * @param array $haystack
     *
     * @return array
     */
    protected function retrieve(int $key, array $haystack): array
    {
        /** @var Response $response */

        $response = $haystack[$key]['value'];

        if (200 === $response->getStatusCode()) {
            return json_decode($response->getBody()->getContents(), true)['package'];
        }

        Log::error('[package:sync:information] Failed to fetch package information.', [
            'id' => $key,
        ]);

        return [];
    }

    /**
     * Get package latest version except dev.
     *
     * @param array $versions
     *
     * @return null|string
     */
    protected function latestVersion(array $versions): ?string
    {
        $versions = Arr::pluck($versions, 'version');

        $versions = array_filter($versions, function ($version) {
            return 'stable' === VersionParser::parseStability($version);
        });

        return Arr::first(Semver::rsort($versions));
    }

    /**
     * Get package minimum php required version.
     *
     * @param string|null $constraint
     *
     * @return null|string
     */
    protected function minPhpVersion(string $constraint = null): ?string
    {
        $versions = [
            '7.4', '7.3', '7.2', '7.1', '7.0',
            '5.6', '5.5', '5.4', '5.3',
        ];

        return $this->minVersion($versions, $constraint);
    }

    /**
     * Get package minimum laravel required version.
     *
     * @param string|null $constraint
     *
     * @return null|string
     */
    protected function minLaravelVersion(string $constraint = null): ?string
    {
        $versions = [
            '6.2', '6.1', '6.0',
            '5.8', '5.7', '5.6', '5.5', '5.4', '5.3', '5.2', '5.1', '5.0',
            '4.2', '4.1', '4.0',
        ];

        return $this->minVersion($versions, $constraint);
    }

    /**
     * Get minimum satisfied version.
     *
     * @param array       $versions
     * @param string|null $constraint
     *
     * @return null|string
     */
    protected function minVersion(array $versions, string $constraint = null): ?string
    {
        if (is_null($constraint)) {
            return null;
        }

        return Arr::last(Semver::satisfiedBy($versions, $constraint));
    }
}
