<?php

use App\Http\Controllers\API\V2\PurchaseControllerV2;
use App\Http\Controllers\API\V2\SubscriptionControllerV2;
use Illuminate\Support\Facades\Route;

// MENNADOS PEDADAA
Route::prefix('v2')->group(function () {
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionControllerV2::class, 'index'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Get all subscriptions')
            ->description('this endpoint returns all the subscriptions available in the system, with their discounts if any, and the price after discount');

        Route::post('/redeem', [SubscriptionControllerV2::class, 'redeem'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Redeem a subscription card')
            ->description('this endpoint takes the code of the card and redeems it for the user, it errors if the card is already used by the user, if its used by another user, if the user already subscribed to the same subscription so there is no need to subscribe again');
    });


    Route::prefix('purchase')->group(function () {
        Route::get('/check-price', [PurchaseControllerV2::class, 'checkPrice'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Check subscription price with discounts & promo code')
            ->description('Returns original price, subscription discount (percentage & amount), promo code discount (percentage & amount), and combined discount (percentage & amount). Promo code applied only if active within its date range.');
    });
});
