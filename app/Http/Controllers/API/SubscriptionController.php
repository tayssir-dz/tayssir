<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Subscription\RedeemRequest;
use App\Http\Requests\API\Subscription\UnsubscribeRequest;
use App\Models\SubscriptionCard;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Validator;
use G4T\Swagger\Attributes\SwaggerSection;

#[SwaggerSection("This section oversees subscription management, allowing users to view their subscriptions, redeem subscription cards, and unsubscribe from active subscriptions. It enforces checks to ensure valid subscriptions and handles user-specific subscription actions securely, including error handling for invalid or already used subscription codes.")]
class SubscriptionController extends BaseController
{
    public function redeem(RedeemRequest $request)
    {
        $code = $request->input('code');
        $user = $request->user();
        if ($user->subscriptionCard !== null) {
            return $this->sendError("User already has a subscription card");
        }
        $subscriptionCard = SubscriptionCard::where('code', $code)->first();
        if ($subscriptionCard === null) {
            return $this->sendError("Invalid code");
        }
        if ($subscriptionCard->user_id === $user->id) {
            return $this->sendError("Subscription card already redeemed by user");
        }
        if ($subscriptionCard->user_id !== null) {
            return $this->sendError("Subscription card already redeemed");
        }
        try {
            $subscriptionCard->user_id = $user->id;
            $subscriptionCard->redeemed_at = now();
            $subscriptionCard->save();
            return $this->sendResponse(message: "Subscription card redeemed successfully");
        } catch (UniqueConstraintViolationException $e) {
            return $this->sendError(error: "User already subscribed", code: 409);
        } catch (Exception $e) {
            return $this->sendError("An error occurred", $e->getMessage(), 500);
        }
    }

    public function userSubscriptions(Request $request)
    {
        $user = $request->user();
        $subscriptionCards = $user->subscriptionCards;

        $subscriptions = $subscriptionCards->map(function ($subscriptionCard) {
            if ($subscriptionCard->subscription && Carbon::now()->lessThan($subscriptionCard->subscription->ending_date)) {
                return [
                    "id" => $subscriptionCard->subscription->id,
                    "name" => $subscriptionCard->subscription->name,
                    "description" => $subscriptionCard->subscription->description,
                    "ending_date" => $subscriptionCard->subscription->ending_date
                ];
            }
        })->filter()->toArray();

        return $this->sendResponse(["subscriptions" => $subscriptions]);
    }

    public function unsubscribe(UnsubscribeRequest $request)
    {
        $subscription_id = $request->input('id');
        $user = $request->user();

        $subscriptionCard = $user->subscriptionCards->where('subscription_id', $subscription_id)->first();

        if ($subscriptionCard === null) {
            return $this->sendError("User is not subscribed to this subscription");
        }

        if ($subscriptionCard->subscription->ending_date->lessThan(Carbon::now())) {
            return $this->sendError("Subscription already expired");
        }

        $subscriptionCard->user_id = null;
        $subscriptionCard->redeemed_at = null;

        $subscriptionCard->save();

        return $this->sendResponse("User unsubscribed successfully");
    }
}
