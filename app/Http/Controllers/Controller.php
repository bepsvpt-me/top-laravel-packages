<?php

namespace App\Http\Controllers;

use App\Download;
use App\Package;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Controller extends BaseController
{
    /**
     * @var Carbon
     */
    protected $ttl;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->ttl = now()->addHours(1);
    }

    /**
     * Exclude official included packages.
     *
     * @param Collection $collection
     *
     * @return Collection
     */
    protected function exclude(Collection $collection): Collection
    {
        $official = [
            'barryvdh/laravel-cors',
            'facade/ignition',
            'fruitcake/laravel-cors',
            'nunomaduro/collision',
        ];

        return $collection->filter(function ($model) use ($official) {
            if ($model instanceof Package) {
                $name = $model->name;
            } elseif ($model instanceof Download) {
                $name = $model->package->name;
            } else {
                return true;
            }

            if (in_array($name, $official, true)) {
                return false;
            }

            return !Str::contains($name, ['laravel/', 'illuminate/']);
        });
    }
}
