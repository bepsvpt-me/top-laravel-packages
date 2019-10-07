<?php

namespace App\Http\Controllers;

use App\Download;
use App\Package;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Package list.
     *
     * @return View
     */
    public function index(): View
    {
        $packages = Cache::remember('package-list', 60 * 60, function () {
            $packages = Package::query()
                ->orderByDesc('downloads')
                ->orderByDesc('favers')
                ->get();

            return $this->filterOfficialPackages($packages);
        });

        return view('home', compact('packages'));
    }

    /**
     * Package downloads ranking.
     *
     * @param string $type
     * @param string $date
     *
     * @return View
     */
    public function ranking(string $type, string $date): View
    {
        try {
            $date = DateTime::createFromFormat($this->format($type), $date)->format('Y-m-d');
        } catch (Exception $e) {
            abort(404);
        }

        $key = sprintf('package-ranking-%s-%s', $type, $date);

        $ranks = Cache::remember($key, 60 * 60, function () use ($type, $date) {
            $ranks = Download::with('package:packages.id,name,url,description')
                ->where('type', $type)
                ->where('date', $date)
                ->orderByDesc('downloads')
                ->get();

            return $this->filterOfficialPackages($ranks);
        });

        return view('rank', compact('ranks'));
    }

    /**
     * Get type format.
     *
     * @param string $type
     *
     * @return string
     */
    protected function format(string $type): string
    {
        switch ($type) {
            case 'daily':
            case 'weekly':
                return '!Y-m-d';
            case 'monthly':
                return '!Y-m';
            case 'yearly':
                return '!Y';
            default:
                return abort(404);
        }
    }

    /**
     * Filter official packages.
     *
     * @param Collection $collection
     *
     * @return Collection
     */
    protected function filterOfficialPackages(Collection $collection): Collection
    {
        return $collection->filter(function ($model) {
            if ($model instanceof Package) {
                $name = $model->name;
            } elseif ($model instanceof Download) {
                $name = $model->package->name;
            } else {
                return true;
            }

            return !Str::contains($name, ['laravel/', 'illuminate/']);
        });
    }
}
