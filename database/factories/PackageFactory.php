<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(App\Package::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->sentence,
        'url' => $faker->url,
        'repository' => $faker->url,
        'downloads' => $faker->numberBetween(),
        'favers' => $faker->numberBetween(),
        'weights' => $faker->numberBetween(1, 15),
    ];
});
