<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;

class ResponseController
{
    public static function WilayaRes($wilaya)
    {
        return [
            "id" => $wilaya->id,
            "name" => $wilaya->name,
            "arabic_name" => $wilaya->arabic_name,
        ];
    }

    public static function CommuneRes($commune)
    {
        return [
            "id" => $commune->id,
            "name" => $commune->name,
            "arabic_name" => $commune->arabic_name,
        ];
    }
    public static function userRes($user)
    {
        return [
            'id' => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "image_url" => $user->avatar_url,
            "phone_number" => $user->phone_number,
            "email_verified" => $user->email_verified_at !== null,
            "wilaya" => $user->wilaya ? ResponseController::WilayaRes($user->wilaya) : null,
            "commune" => $user->wilaya && $user->commune ? ResponseController::CommuneRes($user->commune) : null,
            "division" => $user->division,
            "subscriptions" => $user->subscriptions,
            // 'subscribed' => $subscriptionCard && $subscriptionCard->subscription && Carbon::now()->lessThan($user->subscriptionCard->subscription->ending_date),
        ];
    }


}
