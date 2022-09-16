<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestLanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $available = ['en', 'zh_TW'];

        $locale = $request->getPreferredLanguage($available);

        if ($locale) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
