<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command as BaseCommand;

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

        $this->client = new Client;
    }
}
