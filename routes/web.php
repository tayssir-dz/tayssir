<?php

use App\Mail\welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name("login");

Route::view("/test", "test");

