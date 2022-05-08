<?php

namespace App\Http\Controllers;

use App\Package;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * @extends Controller<Package>
 */
class OverviewController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return View
     */
    public function __invoke(): View
    {
        $key = 'overview';

        $packages = Cache::remember($key, $this->ttl, function () {
            return $this->exclude(
                Package::orderByDesc('downloads')
                    ->orderByDesc('favers')
                    ->get()
            );
        });

        return view('home')->with('packages', $packages);
    }
}
