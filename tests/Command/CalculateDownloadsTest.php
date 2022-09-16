<?php

namespace Tests\Command;

use App\Console\Commands\CalculateDownloads;
use App\Download;
use App\Package;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

class CalculateDownloadsTest extends TestCase
{
    public function testCalculateDownloadsCommand(): void
    {
        $package = Package::factory()->create();

        $this->assertInstanceOf(Package::class, $package);

        $count = 50;

        $days = [];

        while ($count--) {
            $days[$count] = now()->addDays($count)->startOfDay();

            $download = Download::factory()->make(['date' => $days[$count]]);

            $this->assertInstanceOf(Download::class, $download);

            $package->downloads()->save($download);
        }

        $this->assertNotEmpty($days);

        $command = $this->artisan(CalculateDownloads::class);

        $this->assertInstanceOf(PendingCommand::class, $command);

        $command->assertSuccessful();

        $command->run();

        /**
         * Sqlite3 only supports date time format.
         */
        $day = Arr::first($days);

        $this->assertInstanceOf(Carbon::class, $day);

        $day = $day->copy();

        $this->assertDatabaseMissing('downloads', [
            'package_id' => $package->getKey(),
            'date' => $day->startOfWeek()->toDateTimeString(),
            'type' => 'weekly',
        ]);

        $this->assertDatabaseMissing('downloads', [
            'package_id' => $package->getKey(),
            'date' => $day->startOfMonth()->toDateTimeString(),
            'type' => 'monthly',
        ]);

        $this->assertDatabaseMissing('downloads', [
            'package_id' => $package->getKey(),
            'date' => $day->startOfYear()->toDateTimeString(),
            'type' => 'yearly',
        ]);

        $day = Arr::last($days);

        $this->assertInstanceOf(Carbon::class, $day);

        $day = $day->copy();

        $this->assertDatabaseMissing('downloads', [
            'package_id' => $package->getKey(),
            'date' => $day->startOfWeek()->toDateTimeString(),
            'type' => 'weekly',
        ]);

        $this->assertDatabaseMissing('downloads', [
            'package_id' => $package->getKey(),
            'date' => $day->startOfMonth()->toDateTimeString(),
            'type' => 'monthly',
        ]);

        $this->assertDatabaseMissing('downloads', [
            'package_id' => $package->getKey(),
            'date' => $day->startOfYear()->toDateTimeString(),
            'type' => 'yearly',
        ]);

        $command = $this->artisan(CalculateDownloads::class);

        $this->assertInstanceOf(PendingCommand::class, $command);

        $command->assertSuccessful();

        $command->run();
    }
}
