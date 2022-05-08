<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\Download
 *
 * @property int $id
 * @property int $package_id
 * @property string $date
 * @property int $downloads
 * @property string $type
 * @property-read \App\Package|null $package
 * @method static \Database\Factories\DownloadFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Download newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Download newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Download query()
 * @method static \Illuminate\Database\Eloquent\Builder|Download whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Download whereDownloads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Download whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Download wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Download whereType($value)
 * @mixin \Eloquent
 */
	final class IdeHelperDownload {}
}

namespace App{
/**
 * App\Package
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $url
 * @property string $repository
 * @property \Illuminate\Database\Eloquent\Collection|\App\Download[] $downloads
 * @property int $favers
 * @property int|null $dependents
 * @property int|null $github_stars
 * @property int|null $github_watchers
 * @property int|null $github_forks
 * @property int|null $github_open_issues
 * @property string|null $latest_version
 * @property string|null $min_php_version
 * @property string|null $min_laravel_version
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $weights
 * @property-read int|null $downloads_count
 * @method static \Database\Factories\PackageFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDependents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDownloads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereFavers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereGithubForks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereGithubOpenIssues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereGithubStars($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereGithubWatchers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereLatestVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereMinLaravelVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereMinPhpVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereRepository($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereWeights($value)
 * @mixin \Eloquent
 */
	final class IdeHelperPackage {}
}

