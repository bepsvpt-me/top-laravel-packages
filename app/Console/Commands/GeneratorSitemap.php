<?php

namespace App\Console\Commands;

use App\Download;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use stdClass;

class GeneratorSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generator sitemap.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $downloads = DB::table((new Download)->getTable())
                       ->select(['date', 'type'])
                       ->distinct()
                       ->get()
                       ->groupBy('type')
                       ->map(
                           fn (Collection $dates) => $dates->map(
                               fn (stdClass $item) => $item->date,
                           ),
                       );

        $sitemap = Sitemap::create();

        $sitemap->add(
            Url::create(route('home'))
               ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
               ->setPriority(1.0)
        );

        $formats = [
            'daily' => 'Y-m-d',
            'weekly' => 'Y-m-d',
            'monthly' => 'Y-m',
            'yearly' => 'Y',
        ];

        $priorities = [
            'daily' => 0.1,
            'weekly' => 0.3,
            'monthly' => 0.6,
            'yearly' => 0.9,
        ];

        $boundaries = [
            'daily' => now()->subDay()->startOfDay()->subRealMillisecond(),
            'weekly' => now()->subDay()->startOfWeek()->subRealMillisecond(),
            'monthly' => now()->subDay()->startOfMonth()->subRealMillisecond(),
            'yearly' => now()->subDay()->startOfYear()->subRealMillisecond(),
        ];

        foreach ($downloads as $type => $dates) {
            $format = $formats[$type];

            $priority = $priorities[$type];

            foreach ($dates as $date) {
                $time = Carbon::parse($date);

                $sitemap->add(
                    Url::create(route('ranking', [
                        'type' => $type,
                        'date' => $time->format($format),
                    ]))
                       ->setLastModificationDate(
                           $time->isBefore($boundaries[$type])
                               ? $time
                               : $boundaries['daily'],
                       )
                       ->setChangeFrequency(
                           $time->isBefore($boundaries[$type])
                               ? Url::CHANGE_FREQUENCY_YEARLY
                               : Url::CHANGE_FREQUENCY_DAILY,
                       )
                       ->setPriority($priority)
                );
            }
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        return self::SUCCESS;
    }
}
