<?php

namespace App\Services\Purchase;

use App\Models\PromoCode;
use App\Models\Subscription;
use App\Models\SubscriptionCard;
use App\Models\User;

class SubscriptionActivationService
{

    public static function ActivateSubscriptionForUser($subscription_id, $user_id): bool
    {
        try {
            $code = rand(100000000000, 999999999999);
            SubscriptionCard::query()->create([
                'code' => $code,
                'user_id' => $user_id,
                'subscription_id' =>  $subscription_id,
                'redeemed_at' => now(),
            ]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
