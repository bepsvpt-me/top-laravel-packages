<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webmozart\Assert\Assert;

/**
 * @mixin IdeHelperPackage
 */
class Package extends Model
{
    use HasFactory;

    public const TOTAL_WEIGHTS = 15;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var bool
     */
    protected $guarded = false;

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

        Assert::nullOrIsInstanceOf($download, Download::class);

        if ($download === null) {
            return '';
        }

        return $download->date;
    }
}
