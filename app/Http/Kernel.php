<?php

namespace App\Http;

use App\Http\Middleware\RequestLanguageMiddleware;
use Bepsvpt\SecureHeaders\SecureHeadersMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middlewares are run during every request to your application.
     *
     * @var array<int, class-string>
     */
    protected $middleware = [
        SecureHeadersMiddleware::class,
        CheckForMaintenanceMode::class,
        RequestLanguageMiddleware::class,
    ];
}
