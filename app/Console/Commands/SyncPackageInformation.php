<?php

namespace App\Console\Commands;

use App\Package;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Throwable;
use function GuzzleHttp\Promise\unwrap;

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
        $chunks = $this->packages();

        $this->output->progressStart($chunks->sum->count());

        /** @var Collection $packages */

        foreach ($chunks as $packages) {
            try {
                $responses = $this->responses($packages);
            } catch (Throwable $e) {
                $this->fatal(
                    'Failed to sync package information.',
                    $packages->pluck('id')->toArray()
                );

                continue;
            }

            foreach ($responses as $key => $info) {
                /** @var Package $package */

                $package = $packages->where('id', $key)->first();

                $version = $this->latestVersion($info['versions']);

                $requires = $info['versions'][$version]['require'] ?? [];

                $package->update([
                    'dependents' => $info['dependents'],
                    'github_stars' => $info['github_stars'],
                    'github_watchers' => $info['github_watchers'],
                    'github_forks' => $info['github_forks'],
                    'github_open_issues' => $info['github_open_issues'],
                    'latest_version' => $version,
                    'min_php_version' => $this->minPhpVersion($requires['php'] ?? null),
                    'min_laravel_version' => $this->minLaravelVersion($requires['laravel/framework'] ?? ($requires['illuminate/support'] ?? null)),
                ]);
            }

            $this->output->progressAdvance($packages->count());

            sleep(mt_rand(2, 5));
        }

        $this->output->progressFinish();

        $this->info('Package information syncs successfully.');
    }

    /**
     * Get packages filter by weights.
     *
     * @return Collection
     */
    protected function packages(): Collection
    {
        $day = now()->dayOfYear;

        $available = range(1, Package::TOTAL_WEIGHTS);

        $weights = array_filter($available, function (int $weight) use ($day) {
            return 0 === ($day % $weight);
        });

        return Package::query()
            ->whereIn('weights', $weights)
            ->get(['id', 'name'])
            ->chunk(5);
    }

    /**
     * Get package information api urls.
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
            $promises[$package->getKey()] = $this->client->getAsync(
                sprintf('/packages/%s.json', $package->name)
            );
        }

        return array_map(function (Response $response) {
            $content = $response->getBody()->getContents();

            $decoded = json_decode($content, true);

            return $decoded['package'];
        }, unwrap($promises));
    }

    /**
     * Get package latest stable version.
     *
     * @param array<mixed> $versions
     *
     * @return null|string
     */
    protected function latestVersion(array $versions): ?string
    {
        $all = array_keys($versions);

        $stables = array_filter($all, function (string $version) {
            return 'stable' === VersionParser::parseStability($version);
        });

        return Arr::first(Semver::rsort($stables));
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
            '8.0',
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
            '7.0', '6.0',
            '5.8', '5.7', '5.6', '5.5', '5.4', '5.3', '5.2', '5.1', '5.0',
            '4.2', '4.1', '4.0',
        ];

        return $this->minVersion($versions, $constraint);
    }

    /**
     * Get minimum satisfied version.
     *
     * @param array<string> $versions
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
