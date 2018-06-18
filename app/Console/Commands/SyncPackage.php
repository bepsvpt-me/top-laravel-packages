<?php

namespace App\Console\Commands;

use App\Package;
use Carbon\Carbon;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use GuzzleHttp\Promise;
use Illuminate\Database\Eloquent\Collection;
use Log;

class SyncPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:package';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync package information.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packages = $this->packages();

        $packages->chunk(10)->each(function (Collection $packages) {
            $results = Promise\settle($this->asyncUrls($packages))->wait();

            foreach ($packages as $package) {
                if (empty($data = $this->parsePromiseResponse($package->getKey(), $results))) {
                    continue;
                }

                $latestVersion = $this->latestVersion($data['versions']);

                $package->update([
                    'dependents' => $data['dependents'],
                    'github_stars' => $data['github_stars'],
                    'github_watchers' => $data['github_watchers'],
                    'github_forks' => $data['github_forks'],
                    'github_open_issues' => $data['github_open_issues'],
                    'latest_version' => $latestVersion,
                    'min_php_version' => $this->minPhpVersion($data['versions'][$latestVersion]['require']['php'] ?? null),
                    'min_laravel_version' => $this->minLaravelVersion($data['versions'][$latestVersion]['require']['illuminate/support'] ?? null),
                ]);
            }
        });

        $this->info('Command execute successfully.');
    }

    /**
     * Get packages filter by weights.
     *
     * @return Collection
     */
    protected function packages(): Collection
    {
        $day = Carbon::now()->dayOfYear;

        $weights = array_filter(range(1, Package::TOTAL_WEIGHTS), function ($weight) use ($day) {
            return 0 === ($day % $weight);
        });

        return Package::whereIn('weights', $weights)->get(['id', 'url']);
    }

    /**
     * Get package information api urls.
     *
     * @param Collection $packages
     *
     * @return array
     */
    protected function asyncUrls(Collection $packages): array
    {
        $urls = $packages->mapWithKeys(function (Package $package) {
            $url = sprintf('%s.json', $package->url);

            return [$package->getKey() => $this->client->getAsync($url)];
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
    protected function parsePromiseResponse(int $key, array $haystack): array
    {
        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $haystack[$key]['value'];

        if (200 !== $response->getStatusCode()) {
            Log::error('failed to fetch package information', [
                'id' => $key,
            ]);

            return [];
        }

        $content = $response->getBody()->getContents();

        return json_decode($content, true)['package'];
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
        $versions = array_pluck($versions, 'version');

        $versions = array_filter($versions, function ($version) {
            return 'stable' === VersionParser::parseStability($version);
        });

        return array_first(Semver::rsort($versions));
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
        return $this->minVersion(
            ['7.2', '7.1', '7.0', '5.6', '5.5', '5.4', '5.3'],
            $constraint
        );
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
        return $this->minVersion(
            ['5.6', '5.5', '5.4', '5.3', '5.2', '5.1', '5.0', '4.2', '4.1', '4.0'],
            $constraint
        );
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

        return array_last(Semver::satisfiedBy($versions, $constraint));
    }
}
