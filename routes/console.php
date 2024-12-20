<?php

use App\Models\Card;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment("keziz mouayed");
})->purpose('Display an inspiring quote');

// command otp:clean


Artisan::command('otp:clean', function () { })
    ->name("clean otp")
    ->hourly();



// Schedule::call(function () {
//     // dd("hello world");
//     $this->comment("hello world");
// })->name("delete cards")
//     ->everySecond();
