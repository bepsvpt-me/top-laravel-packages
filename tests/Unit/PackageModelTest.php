<?php

namespace Tests\Unit;

use App\Download;
use App\Package;
use Tests\TestCase;

final class PackageModelTest extends TestCase
{
    public function testSyncedAt(): void
    {
        $this->assertSame('', (new Package)->syncedAt());

        /** @var Package $package */

        $package = Package::factory()->create();

        $this->assertSame('', $package->syncedAt());

        $download = $package->downloads()->save(
            Download::factory()->make()
        );

        $this->assertInstanceOf(Download::class, $download);

        $this->assertSame($download->date, $package->syncedAt());
    }
}
