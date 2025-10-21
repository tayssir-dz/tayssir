<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialRedirectController;

Route::get('/social/{platform}', SocialRedirectController::class)
    ->where('platform', 'instagram|facebook|tiktok|youtube|linkedin')
    ->name('social.redirect');
