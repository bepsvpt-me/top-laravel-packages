<?php

namespace Tests\Command;

use App\Download;
use App\Package;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Tests\TestCase;

final class CalculateDownloadsTest extends TestCase
{
    public function testCalculateDownloadsCommand(): void
    {
        /** @var Package $package */

        $package = Package::factory()->create();

        $count = 50;

        $days = [];

        while ($count--) {
            $days[$count] = now()->addDays($count)->startOfDay();

            $package->downloads()->save(
                Download::factory()->make([
                    'date' => $days[$count]
                ])
            );
        }

        $this->artisan('package:calc:downloads')
            ->assertExitCode(0);

        /**
         * Sqlite3 only supports date time format.
         */

        /** @var Carbon $day */

        $day = Arr::first($days)->copy();

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

        $day = Arr::last($days)->copy();

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

        $this->artisan('package:calc:downloads')
            ->assertExitCode(0);
    }
}
