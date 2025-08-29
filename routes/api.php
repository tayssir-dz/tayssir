<?php

use App\Http\Controllers\API\V1\DivisionController;
use App\Http\Controllers\API\V1\ReferralSourceController;
use App\Http\Controllers\API\V1\SubscriptionController;
use Illuminate\Support\Facades\Route;


// MENNADOS PEDADAA
Route::get('divisions', [DivisionController::class, 'index'])
    ->summary('List all divisions')
    ->description('This endpoint returns all available divisions');

Route::get('divisions/{id}', [DivisionController::class, 'show'])
    ->summary('Get division by ID')
    ->description('This endpoint returns a specific division by its ID');

Route::get('subscriptions', [SubscriptionController::class, 'index'])
    ->summary('List all subscriptions')
    ->description('This endpoint returns all available subscriptions with their discounts');

Route::get('subscriptions/{id}', [SubscriptionController::class, 'show'])
    ->summary('Get subscription by ID')
    ->description('This endpoint returns a specific subscription by its ID with associated discounts');

// Referral Sources
Route::get('referral-sources', [ReferralSourceController::class, 'index'])
    ->summary('List referral sources')
    ->description('Returns all referral sources with id, name, icon, and users_count.');
