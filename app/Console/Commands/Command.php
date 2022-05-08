<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Support\Facades\Log;

abstract class Command extends BaseCommand
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = new Client([
            'base_uri' => 'https://packagist.org',
            'connect_timeout' => 30,
            'timeout' => 30,
        ]);
    }

    /**
     * Fatal error.
     *
     * @param string $msg
     * @param array<int|string, mixed> $payload
     *
     * @return void
     */
    protected function fatal(string $msg, array $payload = []): void
    {
        Log::error(
            sprintf('[%s] %s', $this->signature, $msg),
            $payload
        );
    }

    /**
     * Critical error.
     *
     * @param string $msg
     * @param array<mixed> $payload
     *
     * @return void
     */
    protected function critical(string $msg, array $payload = []): void
    {
        Log::critical(
            sprintf('[%s] %s', $this->signature, $msg),
            $payload
        );
    }
}
