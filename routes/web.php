<?php

use App\Http\Controllers\OverviewController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

Route::name('home')
    ->get('/')
    ->uses(OverviewController::class);

Route::name('ranking')
    ->get('/ranking/{type}/{date}')
    ->uses(RankingController::class)
    ->whereIn('type', ['daily', 'weekly', 'monthly', 'yearly'])
    ->where('date', '\d{4}(?:-\d{2}(?:-\d{2})?)?');
