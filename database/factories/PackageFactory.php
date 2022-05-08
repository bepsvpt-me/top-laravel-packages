<?php

namespace Database\Factories;

use App\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, int|string>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->name(),
            'description' => $this->faker->sentence(),
            'url' => $this->faker->url(),
            'repository' => $this->faker->url(),
            'downloads' => $this->faker->numberBetween(),
            'favers' => $this->faker->numberBetween(),
            'weights' => $this->faker->numberBetween(1, 15),
        ];
    }
}
