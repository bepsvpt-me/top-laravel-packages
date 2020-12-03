<?php

use App\Http\Controllers\OverviewController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

Route::name('home')
    ->get('/')
    ->uses(OverviewController::class);

Route::name('ranking')
    ->get('/ranking/{type}/{date}')
    ->uses(RankingController::class);
