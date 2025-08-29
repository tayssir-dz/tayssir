<?php

use App\Http\Controllers\API\AppSettingsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BacController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\ChangeEmailController;
use App\Http\Controllers\API\ContentController;
use App\Http\Controllers\API\ContentWebController;
use App\Http\Controllers\API\EmailVerificationController;
use App\Http\Controllers\API\FlashCardsController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\LeaderBoardController;
use App\Http\Controllers\API\V2\SubscriptionController;
use App\Http\Controllers\API\SummaryController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

// MENNADOS PEDADAA
Route::prefix('v2')->group(function () {
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Get user subscriptions')
            ->description('this returns an array of user subscriptions (id, name, description, ending_date)');

        Route::post('/redeem', [SubscriptionController::class, 'redeem'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Redeem a subscription')
            ->description('this endpoint takes the code of the card and redeems it for the user, it errors if the card is already used by the user, if its used by another user, if the user already subscribed to the same subscription so there is no need to subscribe again');
    });
});
