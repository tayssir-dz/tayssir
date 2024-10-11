<?php

use App\Models\Card;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment("keziz mouayed");
// })->purpose('Display an inspiring quote')->everySecond();

// command otp:clean

Artisan::command('otp:clean', function () { })
    ->name("clean otp")
    ->hourly();



Schedule::call(function () {
    // loop through cards (in an optimized way), to see to change the status based on date values
    // if its expired and activated, then status = "done"
    // if its expired and not activated, then status = "expired"
    // if its not expired and activated, then status = "active"
    // if its not expired and not activated, then status = "idle"
    $cards = Card::where("expires_at", "<", now())->get();
    foreach ($cards as $card) {
        if ($card->expires_at < now()) {
            if ($card->activated_at) {
                $card->status = "done";
            } else {
                $card->status = "expired";
            }
        } else {
            if ($card->activated_at) {
                $card->status = "active";
            } else {
                $card->status = "idle";
            }
        }
        $card->save();
    }

})
    ->name("delete cards")
    ->everyMinute();


// Schedule::call(function () {
//     User::truncate();
// })
//     ->name("delete all users")
//     ->everySecond();
