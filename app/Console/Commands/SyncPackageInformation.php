<?php

namespace App\Console\Commands;

use App\Package;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Throwable;
use Webmozart\Assert\Assert;

/**
 * @template TVersion of array {
 *     name: string,
 *     description: string,
 *     keywords: array<int, string>,
 *     homepage: string,
 *     version: string,
 *     version_normalized: string,
 *     license: array<int, string>,
 *     authors: array<int, array<string, string>>,
 *     source: array<string, string>,
 *     dist: array<string, string>,
 *     type: string,
 *     support: array<string, string>,
 *     funding: array<int, array<string, string>>,
 *     time: string,
 *     autoload: array<string, array<int|string, string>>,
 *     extra: array<string, mixed>,
 *     default-branch: bool,
 *     require: array<string, string>,
 *     require-dev: array<string, string>,
 *     suggest: array<string, string>
 * }
 *
 * @template TVersions of array<string, TVersion>
 *
 * @template TPackage of array {
 *     name: string,
 *     description: string,
 *     time: string,
 *     maintainers: array<int, array<string, string>>,
 *     versions: TVersions,
 *     type: string,
 *     repository: string,
 *     github_stars: int,
 *     github_watchers: int,
 *     github_forks: int,
 *     github_open_issues: int,
 *     language: string,
 *     dependents: int,
 *     suggesters: int,
 *     downloads: array {
 *         total: int,
 *         monthly: int,
 *         daily: int
 *     },
 *     favers: int
 * }
 *
 * @template TInformation of array {
 *     package: TPackage
 * }
 */
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
     * @return void
     */
    public function handle(): void
    {
        $this->output->progressStart(Package::count());

        Package::whereIn('weights', $this->weights())
               ->select(['id', 'name'])
               ->chunk(5, function (Collection $packages) {
                   /** @var Collection<int, Package> $packages */
                   try {
                       $responses = $this->responses($packages);
                   } catch (Throwable $e) {
                       $this->fatal(
                           'Failed to sync package information.',
                           $packages->pluck('id')->toArray()
                       );

                       return;
                   }

                   foreach ($responses as $key => $info) {
                       $package = $packages->where('id', $key)->first();

                       Assert::isInstanceOf($package, Package::class);

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
               });

        $this->output->progressFinish();

        $this->info('Package information syncs successfully.');
    }

    /**
     * Get packages weights filter.
     *
     * @return array<int, int>
     */
    protected function weights(): array
    {
        $day = now()->dayOfYear;

        $available = range(1, Package::TOTAL_WEIGHTS);

        return array_filter($available, function (int $weight) use ($day) {
            return 0 === ($day % $weight);
        });
    }

    /**
     * Get package information api urls.
     *
     * @param  Collection<int, Package>  $packages
     * @return TPackage
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

            /** @var TInformation $decoded */
            $decoded = json_decode($content, true);

            return $decoded['package'];
        }, Utils::unwrap($promises));
    }

    /**
     * Get package latest stable version.
     *
     * @param  TVersions  $versions
     * @return string|null
     */
    protected function latestVersion(array $versions): ?string
    {
        $all = array_keys($versions);

        $stables = array_filter($all, function (string $version) {
            return 'stable' === VersionParser::parseStability($version);
        });

        $latest = Arr::first(Semver::rsort($stables));

        Assert::nullOrString($latest);

        return $latest;
    }

    /**
     * Get package minimum php required version.
     *
     * @param  string|null  $constraint
     * @return string|null
     */
    protected function minPhpVersion(string $constraint = null): ?string
    {
        $versions = [
            '8.2', '8.1', '8.0',
            '7.4', '7.3', '7.2', '7.1', '7.0',
            '5.6', '5.5', '5.4', '5.3',
        ];

        return $this->minVersion($versions, $constraint);
    }

    /**
     * Get package minimum laravel required version.
     *
     * @param  string|null  $constraint
     * @return string|null
     */
    protected function minLaravelVersion(string $constraint = null): ?string
    {
        $versions = [
            '9.0', '8.0', '7.0', '6.0',
            '5.8', '5.7', '5.6', '5.5', '5.4', '5.3', '5.2', '5.1', '5.0',
            '4.2', '4.1', '4.0',
        ];

        return $this->minVersion($versions, $constraint);
    }

    /**
     * Get minimum satisfied version.
     *
     * @param  array<int, string>  $versions
     * @param  string|null  $constraint
     * @return string|null
     */
    protected function minVersion(array $versions, string $constraint = null): ?string
    {
        if (is_null($constraint)) {
            return null;
        }

        $min = Arr::last(Semver::satisfiedBy($versions, $constraint));

        Assert::nullOrString($min);

        return $min;
    }
}
