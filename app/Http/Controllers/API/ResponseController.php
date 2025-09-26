<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;

class ResponseController
{
    public static function WilayaRes($wilaya)
    {
        return [
            'id' => $wilaya->id,
            'name' => $wilaya->name,
            'arabic_name' => $wilaya->arabic_name,
        ];
    }

    public static function CommuneRes($commune)
    {
        return [
            'id' => $commune->id,
            'name' => $commune->name,
            'arabic_name' => $commune->arabic_name,
        ];
    }

    public static function userRes($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'age' => $user->age ? $user->age : null,
            // 'image_url' => config('app.url') . '/storage/' . $user->avatar_url,
            'image_url' => $user->avatar_image,
            'phone_number' => $user->phone_number,
            'email_verified' => $user->email_verified_at !== null,
            'wilaya' => $user->wilaya ? ResponseController::WilayaRes($user->wilaya) : null,
            'commune' => $user->wilaya && $user->commune ? ResponseController::CommuneRes($user->commune) : null,
            'division' => $user->division,
            'subscriptions' => $user->subscriptions,
            'points' => $user->points(),
            "has_new_notifications"  => $user->unreadNotifications()->exists(),
            "new_notifications_count"  => $user->unreadNotifications()->count(),
            // 'subscribed' => $subscriptionCard && $subscriptionCard->subscription && Carbon::now()->lessThan($user->subscriptionCard->subscription->ending_date),
        ];
    }
}
