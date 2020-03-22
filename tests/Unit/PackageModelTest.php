<?php

namespace Tests\Unit;

use App\Download;
use App\Package;
use Tests\TestCase;

class PackageModelTest extends TestCase
{
    public function testSyncedAt(): void
    {
        $this->assertSame('', (new Package)->syncedAt());

        /** @var Package $package */

        $package = factory(Package::class)->create();

        $this->assertSame('', $package->syncedAt());

        $download = $package->downloads()->save(
            factory(Download::class)->make()
        );

        $this->assertInstanceOf(Download::class, $download);

        $this->assertSame($download->date, $package->syncedAt());
    }
}
