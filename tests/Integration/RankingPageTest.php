<?php

namespace Tests\Integration;

use App\Download;
use App\Package;
use Tests\TestCase;

class RankingPageTest extends TestCase
{
    public function testInvalidRoute(): void
    {
        $route = route('ranking', [
            'type' => 'yearly',
            'date' => 'this-is-an-apple',
        ]);

        $this->get($route)->assertNotFound();

        $route = route('ranking', [
            'type' => 'hi-apple',
            'date' => '2020-01-01',
        ]);

        $this->get($route)->assertNotFound();

        $route = route('ranking', [
            'type' => 'daily',
            'date' => '2020-13-01',
        ]);

        $this->get($route)->assertNotFound();
    }

    public function testAntiquityDate(): void
    {
        $route = route('ranking', [
            'type' => 'yearly',
            'date' => '1234',
        ]);

        $this->get($route)->assertNotFound();

        $route = route('ranking', [
            'type' => 'monthly',
            'date' => '1001-10',
        ]);

        $this->get($route)->assertNotFound();

        $route = route('ranking', [
            'type' => 'weekly',
            'date' => '1911-01-01',
        ]);

        $this->get($route)->assertNotFound();

        $route = route('ranking', [
            'type' => 'daily',
            'date' => '1999-12-31',
        ]);

        $this->get($route)->assertNotFound();
    }

    public function testEmptyRecord(): void
    {
        $route = route('ranking', [
            'type' => 'yearly',
            'date' => now()->year,
        ]);

        $this->get($route)
            ->assertSeeText('It seems nothing is here!');
    }

    public function testDailyRecord(): void
    {
        $name = 'hi/apple';

        $package = Package::factory()->create([
            'name' => $name,
        ]);

        $download1 = Download::factory()->create([
            'package_id' => $package->getKey(),
            'date' => '2020-01-01',
        ]);

        $download2 = Download::factory()->create([
            'package_id' => $package->getKey(),
            'date' => '2020-01-02',
        ]);

        $route1 = route('ranking', [
            'type' => 'daily',
            'date' => '2020-01-01',
        ]);

        $this->get($route1)
            ->assertSeeText($name)
            ->assertSeeText(number_format($download1->downloads));

        $route2 = route('ranking', [
            'type' => 'daily',
            'date' => '2020-01-02',
        ]);

        $this->get($route2)
            ->assertSeeText($name)
            ->assertSeeText(number_format($download2->downloads));
    }
}
