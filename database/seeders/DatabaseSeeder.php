<?php

namespace Database\Seeders;

use App\Download;
use App\Package;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /** @var Collection|Package[] $packages */

        $packages = Package::factory()
            ->count(rand(5, 10))
            ->create();

        foreach ($packages as $package) {
            $package->downloads()->saveMany(
                Download::factory()->count(rand(5, 15))->make()
            );
        }
    }
}
