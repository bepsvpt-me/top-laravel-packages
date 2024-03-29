<?php

namespace Tests\Integration;

use App\Package;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function testEmptyRecord(): void
    {
        $this->get('/')
            ->assertSeeText('It seems nothing is here!');
    }

    public function testSomeRecord(): void
    {
        $package1 = Package::factory()->create();

        $this->assertInstanceOf(Package::class, $package1);

        $this->assertIsString($package1->description);

        $package2 = Package::factory()->create();

        $this->assertInstanceOf(Package::class, $package2);

        $this->assertIsString($package2->description);

        $this->get('/')
            ->assertSeeText($package1->name)
            ->assertSeeText($package1->description)
            ->assertSeeText($package2->name)
            ->assertSeeText($package2->description);
    }

    public function testHideOfficialPackages(): void
    {
        $package1 = Package::factory()->create([
            'name' => 'fruitcake/laravel-cors',
        ]);

        $this->assertInstanceOf(Package::class, $package1);

        $this->assertIsString($package1->description);

        $package2 = Package::factory()->create();

        $this->assertInstanceOf(Package::class, $package2);

        $this->assertIsString($package2->description);

        $this->get('/')
            ->assertDontSeeText($package1->name)
            ->assertDontSeeText($package1->description)
            ->assertSeeText($package2->name)
            ->assertSeeText($package2->description);
    }

    public function testRankingLinks(): void
    {
        $month = route('ranking', [
            'type' => 'monthly',
            'date' => now()->subDays(2)->format('Y-m'),
        ]);

        $year = route('ranking', [
            'type' => 'yearly',
            'date' => now()->year,
        ]);

        $this->get('/')
            ->assertSee($month)
            ->assertSee($year);
    }
}
