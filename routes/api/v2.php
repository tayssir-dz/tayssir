<?php

use Illuminate\Support\Facades\Route;

// MENNADOS PEDADAA
Route::prefix('v2')->group(function () {
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [App\Http\Controllers\API\V2\SubscriptionControllerV2::class, 'index'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Get user subscriptions')
            ->description('this returns an array of user subscriptions (id, name, description, ending_date)');

        Route::post('/redeem', [App\Http\Controllers\API\V2\SubscriptionControllerV2::class, 'redeem'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Redeem a subscription')
            ->description('this endpoint takes the code of the card and redeems it for the user, it errors if the card is already used by the user, if its used by another user, if the user already subscribed to the same subscription so there is no need to subscribe again');
    });
});
