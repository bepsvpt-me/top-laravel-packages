<?php

namespace App\Http\Controllers;

use App\Package;
use Cache;

class HomeController extends Controller
{
    /**
     * Homepage.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $packages = Cache::remember('package-list', 60, function () {
            return Package::orderByDesc('downloads')
                ->orderByDesc('favers')
                ->get();
        });

        if ($hideOfficialPackages = request('hide_official_packages', true)) {
            $packages = $packages->filter(function (Package $package) {
                return ! str_contains($package->name, ['laravel/', 'illuminate/']);
            });
        }

        return view('home', compact('packages', 'hideOfficialPackages'));
    }
}
