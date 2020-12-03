<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Download
 *
 * @property int $id
 * @property int $package_id
 * @property string $date
 * @property int $downloads
 * @property string $type
 * @property-read Package $package
 * @method static Builder|Download newModelQuery()
 * @method static Builder|Download newQuery()
 * @method static Builder|Download query()
 * @method static Builder|Download whereDate($value)
 * @method static Builder|Download whereDownloads($value)
 * @method static Builder|Download whereId($value)
 * @method static Builder|Download wherePackageId($value)
 * @method static Builder|Download whereType($value)
 * @mixin Eloquent
 */
final class Download extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the package that owns the download.
     *
     * @return BelongsTo
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
