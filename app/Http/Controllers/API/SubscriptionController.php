<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Subscription\RedeemRequest;
use App\Http\Requests\API\Subscription\UnsubscribeRequest;
use App\Models\Subscription;
use App\Models\SubscriptionCard;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Validator;
use G4T\Swagger\Attributes\SwaggerSection;
use Illuminate\Support\Facades\Hash;

// #[SwaggerSection("This section oversees subscription management, allowing users to view their subscriptions, redeem subscription cards, and unsubscribe from active subscriptions. It enforces checks to ensure valid subscriptions and handles user-specific subscription actions securely, including error handling for invalid or already used subscription codes.")]
class SubscriptionController extends BaseController
{
    /**
     * Display a listing of all subscriptions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subscriptions = Subscription::with('discounts')->get();
        $guestSubscription = Subscription::find(Subscription::GUEST_ID);

        if ($subscriptions->isEmpty() && $guestSubscription) {
            $subscriptions->push($guestSubscription);
        } elseif ($subscriptions->count() > 1 && $guestSubscription) {
            $subscriptions = $subscriptions->filter(function ($subscription) {
                return $subscription->id !== Subscription::GUEST_ID;
            });
        }

        $subscriptions =  $subscriptions->unique('id')->values();
        $subscriptions = $subscriptions->map(function ($subscription) {
            return [
                'id' => $subscription->id,
                'name' => $subscription->name,
                'description' => $subscription->description,
                'price' => $subscription->price / 100,
                'ending_date' => $subscription->ending_date,
                'gradiant_start' => $subscription->gradiant_start,
                'gradiant_end' => $subscription->gradiant_end,
                'bottom_color_at_start' => $subscription->bottom_color_at_start,
                'discounts' => $subscription->discounts->map(function ($discount) {
                    return [
                        'id' => $discount->id,
                        'name' => $discount->name,
                        'description' => $discount->description,
                        'amount' => $discount->amount / 100,
                        'percentage' => $discount->percentage,
                        'from' => $discount->from,
                        'to' => $discount->to,
                    ];
                })
            ];
        });

        return $this->sendResponse($subscriptions, __("response.subscriptions_retrieved_successfully"));
    }

    /**
     * Display the specified subscription.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subscription = Subscription::with('discounts')->find($id);

        if (is_null($subscription)) {
            return $this->sendError(__("response.subscription_not_found"));
        }

        $result = [
            'id' => $subscription->id,
            'name' => $subscription->name,
            'description' => $subscription->description,
            'price' => $subscription->price / 100,
            'ending_date' => $subscription->ending_date,
            'gradiant_start' => $subscription->gradiant_start,
            'gradiant_end' => $subscription->gradiant_end,
            'bottom_color_at_start' => $subscription->bottom_color_at_start,
            'discounts' => $subscription->discounts->map(function ($discount) {
                return [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'description' => $discount->description,
                    'amount' => $discount->amount / 100,
                    'percentage' => $discount->percentage,
                    'from' => $discount->from,
                    'to' => $discount->to,
                ];
            })
        ];

        return $this->sendResponse($result, __("response.subscription_retrieved_successfully"));
    }
    public function redeem(RedeemRequest $request)
    {
        $code = $request->input('card_code');
        $user = $request->user();
        if ($user->subscriptionCard !== null) {
            return $this->sendError(__("response.user_already_has_subscription_card"));
        }
        $subscriptionCard = SubscriptionCard::where('code', $code)->first();
        if ($subscriptionCard === null) {
            return $this->sendError(__("response.invalid_code"));
        }
        if ($subscriptionCard->user_id === $user->id) {
            return $this->sendError(__("response.subscription_card_already_redeemed_by_user"));
        }
        if ($subscriptionCard->user_id !== null) {
            return $this->sendError(__("response.subscription_card_already_redeemed"));
        }
        try {
            $subscriptionCard->user_id = $user->id;
            $subscriptionCard->redeemed_at = now();
            $subscriptionCard->save();
            return $this->sendResponse(message: __("response.subscription_card_redeemed_successfully"));
        } catch (UniqueConstraintViolationException $e) {
            return $this->sendError(error: __("response.user_already_subscribed"), code: 409);
        } catch (Exception $e) {
            return $this->sendError(__("response.an_error_occurred"), $e->getMessage(), 500);
        }
    }

    public function userSubscriptions(Request $request)
    {
        $user = $request->user();
        $subscriptions = $user->active_subscriptions->map(function ($subscription) {
            return [
                "id" => $subscription->id,
                "name" => $subscription->name,
                "description" => $subscription->description,
                "ending_date" => $subscription->ending_date,
                'gradiant_start' => $subscription->gradiant_start,
                'gradiant_end' => $subscription->gradiant_end,
                'bottom_color_at_start' => $subscription->bottom_color_at_start,
            ];
        })->toArray();

        return $this->sendResponse(["subscriptions" => $subscriptions]);
    }

    public function unsubscribe(UnsubscribeRequest $request)
    {
        $subscription_id = $request->input('subscription_id');
        $password = $request->input('password');
        $user = $request->user();

        if (!Hash::check($password, $user->password)) {
            return $this->sendError(__("response.invalid_password"));
        }

        $subscriptionCard = $user->subscriptionCards->where('subscription_id', $subscription_id)->first();

        if ($subscriptionCard === null) {
            return $this->sendError(__("response.user_not_subscribed_to_this_subscription"));
        }

        if ($subscriptionCard->subscription->ending_date->lessThan(Carbon::now())) {
            return $this->sendError(__("response.subscription_already_expired"));
        }

        $subscriptionCard->delete();

        return $this->sendResponse(__("response.subscription_unsubscribed_successfully"));
    }
}
