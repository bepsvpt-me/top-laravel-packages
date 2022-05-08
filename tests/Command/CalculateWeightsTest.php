<?php

namespace Tests\Command;

use App\Console\Commands\CalculateWeights;
use App\Package;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

class CalculateWeightsTest extends TestCase
{
    public function testCalculateWeightsCommand(): void
    {
        Package::factory()
               ->count(20)
               ->create([
                   'weights' => 999,
               ]);

        $this->assertDatabaseHas('packages', [
            'weights' => 999,
        ]);

        $command = $this->artisan(CalculateWeights::class);

        $this->assertInstanceOf(PendingCommand::class, $command);

        $command->assertSuccessful();

        $command->run();

        $weights = range(1, Package::TOTAL_WEIGHTS);

        foreach ($weights as $weight) {
            $this->assertDatabaseHas('packages', [
                'weights' => $weight,
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
