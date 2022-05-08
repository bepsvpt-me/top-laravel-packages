<?php

namespace Database\Seeders;

use App\Download;
use App\Package;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        /** @var Collection<int, Package> $packages */

        $packages = Package::factory()
            ->count(rand(5, 10))
            ->create();

        foreach ($packages as $package) {
            /** @var Collection<int, Download> $downloads */

            $downloads = Download::factory()
                                 ->count(rand(5, 15))
                                 ->make();

            $package->downloads()->saveMany($downloads);
        }
    }
}
