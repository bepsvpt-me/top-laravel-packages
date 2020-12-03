<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class RequestLanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $available = ['en', 'zh_TW'];

        $locale = $request->getPreferredLanguage($available);

        if ($locale) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
