<?php

namespace Database\Factories;

use App\Download;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DownloadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Download::class;

    /**
     * Define the model's default state.
     *
     * @return array<int|string>
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
