<?php

use App\Http\Controllers\API\V2\AppSettingsControllerV2;
use App\Http\Controllers\API\V2\ContentControllerV2;
use App\Http\Controllers\API\V2\NotificationControllerV2;
use App\Http\Controllers\API\V2\ReportControllerV2;
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

        Route::post('/initiate-manual-payment', [PurchaseControllerV2::class, 'initiateManualPayment'])
            ->middleware(['auth:sanctum', 'access', 'email-verified'])
            ->summary('Initiate a manual payment for a subscription')
            ->description('This endpoint allows an authenticated user to submit a manual payment request for a subscription. It requires a subscription ID, an optional promo code, and a mandatory file attachment (e.g., a photo of the bank transfer or receipt). The request will be set to "pending" for administrator review.');

        Route::post('/initiate-chargily', [PurchaseControllerV2::class, 'initiateChargily'])
            ->middleware(['auth:sanctum', 'access', 'email-verified'])
            ->summary('Initiate a Chargily payment and get checkout URL')
            ->description('Creates a pending payment record and Chargily checkout, returning the checkout URL and ID. Client should redirect user to the checkout_url returned.');

        Route::get("/chargily", [PurchaseControllerV2::class, 'chargily'])
            ->middleware(["auth:sanctum", "access"]);
    });

    Route::prefix("notifications")->group(function () {
        Route::get("/", [NotificationControllerV2::class, 'index'])
            ->middleware(['auth:sanctum', 'access',])
            ->summary("Get All Notifications")
            ->description("This endpoint returns all user notifications");
        Route::post("/mark-as-read", [NotificationControllerV2::class, 'markAsRead'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary("Mark Notifications as Read")
            ->description("This endpoint marks one or many notifications as read.");

        Route::post("/mark-as-unread", [NotificationControllerV2::class, 'markAsUnread'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary("Mark Notifications as Unread")
            ->description("This endpoint marks one or many notifications as unread.");

        Route::post("/delete", [NotificationControllerV2::class, 'delete'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary("Delete Notifications")
            ->description("This endpoint deletes one or many notifications for the authenticated user.");
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [AppSettingsControllerV2::class, 'index'])
            ->summary('app settings')
            ->description('This endpoint returns an object containing app settings.');
        Route::get('/separated', [AppSettingsControllerV2::class, 'separated'])
            ->summary('app settings (structured)')
            ->description('This endpoint returns an object containing app settings, same content as the the previous endpoint, but app variables and payment variables are separated.');
    });

    Route::prefix('content')->group(function () {
        Route::get('/', [ContentControllerV2::class, 'getUserContent'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Get user content')
            ->description('This endpoint returns the content associated with the authenticated user.');
    });

    Route::prefix('reports')->group(function () {
        Route::post('/question', [ReportControllerV2::class, 'reportQuestion'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Report a question')
            ->description('Creates a report entry for a question with an optional description.');

        Route::post('/contact', [ReportControllerV2::class, 'submitContactForm'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Submit a contact form')
            ->description('Submits a contact form with optional subject and metadata. Auth optional; user_id is stored when available.');
    });
});
