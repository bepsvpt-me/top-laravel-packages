<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperDownload
 */
class Download extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var bool
     */
    protected $guarded = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the package that owns the download.
     *
     * @return BelongsTo<Package, Download>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
