<?php

namespace Tests\Unit;

use App\Download;
use App\Package;
use Tests\TestCase;

class PackageModelTest extends TestCase
{
    public function testSyncedAt(): void
    {
        $this->assertSame('', (new Package())->syncedAt());

        $package = Package::factory()->create();

        $this->assertInstanceOf(Package::class, $package);

        $this->assertSame('', $package->syncedAt());

        $model = Download::factory()->make();

        $this->assertInstanceOf(Download::class, $model);

        $download = $package->downloads()->save($model);

        $this->assertInstanceOf(Download::class, $download);

        $this->assertSame($download->date, $package->syncedAt());
    }
}
