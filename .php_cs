<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/app')
    ->in(__DIR__ . '/config')
    ->in(__DIR__ . '/database')
    ->in(__DIR__ . '/routes')
    ->in(__DIR__ . '/tests')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return Config::create()
    ->setFinder($finder)
    ->setRules([
        '@PSR2' => true,
        'psr4' => true,
    ])
    ->setLineEnding(PHP_EOL)
    ->setRiskyAllowed(true);
