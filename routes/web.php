<?php

use App\Mail\welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name("login");

Route::get('/testing', function () {
    return view('testing');
})->name('testing');
