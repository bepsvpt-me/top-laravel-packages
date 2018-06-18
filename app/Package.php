<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function downloads()
    {
        return $this->hasMany(Download::class);
    }
}
