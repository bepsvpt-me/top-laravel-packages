<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Package
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $repository
 * @property Collection|Download[] $downloads
 * @property int $favers
 * @property int|null $dependents
 * @property int|null $github_stars
 * @property int|null $github_watchers
 * @property int|null $github_forks
 * @property int|null $github_open_issues
 * @property string|null $latest_version
 * @property string|null $min_php_version
 * @property string|null $min_laravel_version
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $description
 * @property int $weights
 * @property-read int|null $downloads_count
 * @method static Builder|Package newModelQuery()
 * @method static Builder|Package newQuery()
 * @method static Builder|Package query()
 * @method static Builder|Package whereCreatedAt($value)
 * @method static Builder|Package whereDependents($value)
 * @method static Builder|Package whereDescription($value)
 * @method static Builder|Package whereDownloads($value)
 * @method static Builder|Package whereFavers($value)
 * @method static Builder|Package whereGithubForks($value)
 * @method static Builder|Package whereGithubOpenIssues($value)
 * @method static Builder|Package whereGithubStars($value)
 * @method static Builder|Package whereGithubWatchers($value)
 * @method static Builder|Package whereId($value)
 * @method static Builder|Package whereLatestVersion($value)
 * @method static Builder|Package whereMinLaravelVersion($value)
 * @method static Builder|Package whereMinPhpVersion($value)
 * @method static Builder|Package whereName($value)
 * @method static Builder|Package whereRepository($value)
 * @method static Builder|Package whereUpdatedAt($value)
 * @method static Builder|Package whereUrl($value)
 * @method static Builder|Package whereWeights($value)
 * @mixin Eloquent
 */
final class Package extends Model
{
    use HasFactory;

    const TOTAL_WEIGHTS = 15;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = [];

    /**
     * Get the downloads for the package.
     *
     * @return HasMany
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Get package synced at info.
     *
     * @return string
     */
    public function syncedAt(): string
    {
        if (!$this->exists) {
            return '';
        }

        $download = $this->downloads()
            ->where('type', 'daily')
            ->orderByDesc('date')
            ->first(['date']);

        return optional($download)->date ?: '';
    }
}
