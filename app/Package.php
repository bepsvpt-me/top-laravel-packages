<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperPackage
 */
class Package extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TOTAL_WEIGHTS = 15;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var bool
     */
    protected $guarded = false;

    /**
     * @return string
     */
    public function getInfoUriAttribute(): string
    {
        return 'https://repo.packagist.org/p2/' . $this->name . '.json';
    }

    /**
     * @return string
     */
    public function getStatsUriAttribute(): string
    {
        $date = $this->downloads()
                     ->where('type', 'daily')
                     ->latest('date')
                     ->first(['date'])
                     ?->date;

        return sprintf(
            'https://packagist.org/packages/%s/stats/all.json?average=daily&from=%s',
            $this->name,
            $date ?: '',
        );
    }

    /**
     * Get the downloads for the package.
     *
     * @return HasMany<Download>
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param  Builder<Package>  $query
     * @return Builder<Package>
     */
    public function scopeUnofficial(Builder $query): Builder
    {
        return $query
            ->whereNot('name', 'like', 'laravel/%')
            ->whereNot('name', 'like', 'illuminate/%')
            ->whereNotIn('name', [
                'barryvdh/laravel-cors',
                'facade/ignition',
                'fruitcake/laravel-cors',
                'fruitcake/php-cors',
                'nunomaduro/collision',
                'spatie/ignition',
                'spatie/laravel-ignition',
            ]);
    }
}
