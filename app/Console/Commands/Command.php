<?php

namespace App\Console\Commands;

use Illuminate\Console\Command as BaseCommand;
use Illuminate\Support\Facades\Log;

abstract class Command extends BaseCommand
{
    protected string $userAgent = 'https://github.com/bepsvpt-me/top-laravel-packages';

    /**
     * Fatal error.
     *
     * @param  string  $msg
     * @param  array<int|string, mixed>  $payload
     * @return void
     */
    protected function fatal(string $msg, array $payload = []): void
    {
        Log::error(
            sprintf('[%s] %s', $this->signature, $msg),
            $payload
        );
    }
}
