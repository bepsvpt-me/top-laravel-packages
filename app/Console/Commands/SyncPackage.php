<?php

namespace App\Console\Commands;

use App\Package;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class SyncPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:package {package*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync package information.';

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
        $packages = Package::whereIn('name', $this->argument('package'))->get(['id', 'url']);

        $packages->chunk(10)->each(function (Collection $packages) {
            $results = Promise\settle($this->asyncUrls($packages))->wait();

            foreach ($packages as $package) {
                $response = $results[$package->getKey()]['value'];

                $content = $response->getBody()->getContents();

                $data = json_decode($content, true)['package'];

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

        $this->info('Packages sync successfully.');
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
