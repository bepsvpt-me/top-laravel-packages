<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;

abstract class Command extends \Illuminate\Console\Command
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
