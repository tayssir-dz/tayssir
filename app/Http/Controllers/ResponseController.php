<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;

class ResponseController
{
    public static function userRes($user)
    {
        // $subscriptionCards = $user->subscriptionCards;

        // // for each subscription card, check if the subscription is still active and return an array of subscription names

        // $subscriptionNames = $subscriptionCards->map(function ($subscriptionCard) {
        //     if ($subscriptionCard->subscription && Carbon::now()->lessThan($subscriptionCard->subscription->ending_date)) {
        //         return $subscriptionCard->subscription->name;
        //     }
        // })->filter()->toArray();
        return [
            'id' => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "image_url" => $user->avatar_url,
            "phone_number" => $user->phone_number,
            "email_verified" => $user->email_verified_at !== null,
            // "subscriptions" => $subscriptionNames,
            // 'subscribed' => $subscriptionCard && $subscriptionCard->subscription && Carbon::now()->lessThan($user->subscriptionCard->subscription->ending_date),
        ];
    }
}
