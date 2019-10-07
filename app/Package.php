<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $url
 */
class Package extends Model
{
    const TOTAL_WEIGHTS = 15;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
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
}
