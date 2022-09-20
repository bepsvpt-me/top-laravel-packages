<?php

namespace App\Http\Controllers;

use App\Download;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RankingController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  string  $type
     * @param  string  $date
     * @return View
     */
    public function __invoke(string $type, string $date): View
    {
        if (($time = $this->check($type, $date)) === null) {
            throw new NotFoundHttpException();
        }

        $key = sprintf('ranking-%s-%s', $type, $date);

        $ttl = $this->ttl($type, $time);

        $ranks = Cache::remember($key, $ttl,
            fn () => Download::with('package')
                            ->whereHas('package',
                                fn ($query) => $query->unofficial(),
                            )
                            ->where('type', $type)
                            ->where('date', $time->toDateString())
                            ->orderByDesc('downloads')
                            ->get(),
        );

        return view('rank')->with('ranks', $ranks);
    }

    /**
     * Check type and date is valid or not.
     *
     * @param  string  $type
     * @param  string  $date
     * @return Carbon|null
     */
    protected function check(string $type, string $date): ?Carbon
    {
        try {
            $target = Carbon::createFromFormat(
                $this->format($type),
                $date,
            );
        } catch (Exception) {
            return null;
        }

        if ($target === false) {
            return null;
        }

        $errors = Carbon::getLastErrors();

        if ($errors['warning_count'] || $errors['error_count']) {
            return null;
        }

        $from = '2012-05-31';

        $to = now()->startOfDay();

        if (!$target->isBetween($from, $to)) {
            return null;
        }

        return $target;
    }

    /**
     * Get format by type.
     *
     * @param  string  $type
     * @return string
     */
    protected function format(string $type): string
    {
        return match ($type) {
            'daily', 'weekly' => '!Y-m-d',
            'monthly' => '!Y-m',
            'yearly' => '!Y',
            default => '',
        };
    }

    /**
     * Get cache ttl.
     *
     * @param  string  $type
     * @param  Carbon  $time
     * @return Carbon
     */
    protected function ttl(string $type, Carbon $time): Carbon
    {
        $default = now()->startOfDay()->addHours(3)->addDay();

        if ($time->isAfter(now()->startOfDay()->subDays())) {
            return $default;
        }

        $group = match ($type) {
            'daily' => 'Day',
            'weekly' => 'Week',
            'monthly' => 'Month',
            'yearly' => 'Year',
            default => 'Century',
        };

        $method = Str::of('isSame')
                     ->append($group)
                     ->toString();

        return now()->{$method}($time)
            ? $default
            : now()->addYears(10);
    }
}
