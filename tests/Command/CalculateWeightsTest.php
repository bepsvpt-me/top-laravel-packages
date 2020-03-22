<?php

namespace Tests\Command;

use App\Download;
use App\Package;
use Tests\TestCase;

class CalculateWeightsTest extends TestCase
{
    public function testCalculateWeightsCommand(): void
    {
        $packages = factory(Package::class, 20)
            ->create([
                'weights' => 999,
            ]);

        /** @var Package $package */

        foreach ($packages as $package) {
            $package->downloads()->saveMany(
                factory(Download::class, 3)->make()
            );
        }

        $this->assertDatabaseHas('packages', [
            'weights' => 999,
        ]);

        $this->artisan('package:calc:weights')
            ->assertExitCode(0);

        foreach (range(1, 15) as $weights) {
            $this->assertDatabaseHas('packages', [
                'weights' => $weights,
            ]);
        }

        $this->assertDatabaseMissing('packages', [
            'weights' => 0,
        ]);

        $this->assertDatabaseMissing('packages', [
            'weights' => 999,
        ]);
    }
}
