<?php

namespace App\Http\Controllers;

use App\Package;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

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

        $ttl = now()->startOfDay()->addHour()->addDay();

        $packages = Cache::remember($key, $ttl,
            fn () => Package::unofficial()
                           ->orderByDesc('downloads')
                           ->orderByDesc('favers')
                           ->get(),
        );

        return view('home')->with('packages', $packages);
    }
}
