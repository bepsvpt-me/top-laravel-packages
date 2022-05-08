<?php

namespace Database\Factories;

use App\Download;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Download>
 */
class DownloadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, int|string>
     */
    public function definition(): array
    {
        return [
            'package_id' => $this->faker->randomNumber(),
            'date' => $this->faker->unique()->date(),
            'downloads' => $this->faker->randomNumber(),
            'type' => 'daily',
        ];
    }
}
