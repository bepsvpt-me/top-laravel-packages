<?php

namespace Database\Factories;

use App\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Package::class;

    /**
     * Define the model's default state.
     *
     * @return array<int|string>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'url' => $this->faker->url,
            'repository' => $this->faker->url,
            'downloads' => $this->faker->numberBetween(),
            'favers' => $this->faker->numberBetween(),
            'weights' => $this->faker->numberBetween(1, 15),
        ];
    }
}
