<?php

Route::get('/', 'HomeController@index')->name('home');
Route::get('/ranking/{type}/{date}', 'HomeController@ranking')->name('ranking');
