<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');

Route::get('/auth/login', function () {
    return view('app');
})->name('login');

Route::get('/{path?}', function () {
    return view('app');
})->where('path', '.*');
