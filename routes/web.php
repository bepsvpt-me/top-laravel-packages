<?php

use Illuminate\Support\Facades\Route;

Route::name('home')
    ->get('/')
    ->uses('HomeController@index');

Route::name('ranking')
    ->get('/ranking/{type}/{date}')
    ->uses('HomeController@ranking');
