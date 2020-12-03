<?php

namespace App\Http\Controllers;

use App\Download;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class RankingController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param string $type
     * @param string $date
     *
     * @return View
     */
    public function __invoke(string $type, string $date): View
    {
        if (!$this->check($type, $date)) {
            throw new NotFoundHttpException;
        }

        $key = sprintf('ranking-%s-%s', $type, $date);

        $ranks = Cache::remember($key, $this->ttl, function () use ($type, $date) {
            return $this->exclude(
                Download::with('package:packages.id,name,url,description')
                    ->where('type', $type)
                    ->where('date', $date)
                    ->orderByDesc('downloads')
                    ->get()
            );
        });

        return view('rank')->with('ranks', $ranks);
    }

    /**
     * Check type and date is valid or not.
     *
     * @param string $type
     * @param string $date
     *
     * @return bool
     */
    protected function check(string $type, string $date): bool
    {
        try {
            $target = Carbon::createFromFormat(
                $this->format($type),
                $date
            );
        } catch (Exception $e) {
            return false;
        }

        $from = '2012-05-31';

        $to = now()->startOfDay();

        return $target && $target->isBetween($from, $to);
    }

    /**
     * Get format by type.
     *
     * @param string $type
     *
     * @return string
     */
    protected function format(string $type): string
    {
        switch ($type) {
            case 'daily':
            case 'weekly':
                return '!Y-m-d';
            case 'monthly':
                return '!Y-m';
            case 'yearly':
                return '!Y';
            default:
                return '';
        }
    }
}
