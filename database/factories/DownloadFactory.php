<?php

use App\Download;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(Download::class, function (Faker $faker) {
    return [
        'package_id' => $faker->randomNumber(),
        'date' => $faker->date(),
        'downloads' => $faker->randomNumber(),
        'type' => 'daily',
    ];
});
