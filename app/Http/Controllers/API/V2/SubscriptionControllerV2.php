<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\API\Subscription\RedeemRequest;
use App\Http\Requests\API\Subscription\UnsubscribeRequest;
use App\Models\Subscription;
use App\Models\SubscriptionCard;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\Group;
use Exception;
use G4T\Swagger\Attributes\SwaggerSection;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Hash;

#[Group('Subscription Management APIs', weight: 1)]
class SubscriptionControllerV2 extends BaseController
{
    /**
     * Display a listing of all subscriptions.
     *
     * this endpoint returns all the subscriptions available in the system, with their discounts if any, and the price after discount
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

        $subscriptions = $subscriptions->unique('id')->values();
        $subscriptions = $subscriptions->map(function ($subscription) {
            return [
                'id' => $subscription->id,
                'name' => $subscription->name,
                'description' => $subscription->description,
                'price' => $subscription->price / 100,
                'price_after_discount' => $subscription->price_after_discount / 100,
                'discount_amount' => $subscription->discount_amount / 100,
                'discount_percentage' => $subscription->discount_percentage,
                'ending_date' => $subscription->ending_date,
                'gradiant_start' => $subscription->gradiant_start,
                'gradiant_end' => $subscription->gradiant_end,
                'bottom_color_at_start' => $subscription->bottom_color_at_start,
                'discounts' => $subscription->discounts->map(function ($discount) {
                    return [
                        'id' => $discount->id,
                        'name' => $discount->name,
                        'description' => $discount->description,
                        'amount' => 0,
                        'percentage' => $discount->percentage,
                        'from' => $discount->from,
                        'to' => $discount->to,
                    ];
                }),
            ];
        });

        return $this->sendResponse($subscriptions, __('response.subscriptions_retrieved_successfully'));
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
            return $this->sendError(__('response.subscription_not_found'));
        }

        $result = [
            'id' => $subscription->id,
            'name' => $subscription->name,
            'description' => $subscription->description,
            'price' => $subscription->price / 100,
            'price_after_discount' => $subscription->price_after_discount / 100,
            'discount_amount' => $subscription->discount_amount / 100,
            'discount_percentage' => $subscription->discount_percentage,
            'ending_date' => $subscription->ending_date,
            'gradiant_start' => $subscription->gradiant_start,
            'gradiant_end' => $subscription->gradiant_end,
            'bottom_color_at_start' => $subscription->bottom_color_at_start,
            'discounts' => $subscription->discounts->map(function ($discount) {
                return [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'description' => $discount->description,
                    'amount' => 0,
                    'percentage' => $discount->percentage,
                    'from' => $discount->from,
                    'to' => $discount->to,
                ];
            }),
        ];

        return $this->sendResponse($result, __('response.subscription_retrieved_successfully'));
    }

    /**
     * Redeem a subscription card.
     *
     * This endpoint takes the code of the card and redeems it for the user, it errors if the card is already used by the user, if its used by another user, if the user already subscribed to the same subscription so there is no need to subscribe again.
     */
    public function redeem(RedeemRequest $request)
    {
        $code = $request->input('card_code');
        $user = $request->user();
        if ($user->subscriptionCard !== null) {
            return $this->sendError(__('response.user_already_has_subscription_card'));
        }
        $subscriptionCard = SubscriptionCard::where('code', $code)->first();
        if ($subscriptionCard === null) {
            return $this->sendError(__('response.invalid_code'));
        }
        if ($subscriptionCard->user_id === $user->id) {
            return $this->sendError(__('response.subscription_card_already_redeemed_by_user'));
        }
        if ($subscriptionCard->user_id !== null) {
            return $this->sendError(__('response.subscription_card_already_redeemed'));
        }
        try {
            $subscriptionCard->user_id = $user->id;
            $subscriptionCard->redeemed_at = now();
            $subscriptionCard->save();

            return $this->sendResponse(message: __('response.subscription_card_redeemed_successfully'));
        } catch (UniqueConstraintViolationException $e) {
            return $this->sendError(error: __('response.user_already_subscribed'), code: 409);
        } catch (Exception $e) {
            return $this->sendError(__('response.an_error_occurred'), $e->getMessage(), 500);
        }
    }

    /**
     * Get user subscriptions.
     *
     * This returns an array of user subscriptions (id, name, description, ending_date).
     */
    public function userSubscriptions(Request $request)
    {
        $user = $request->user();
        $subscriptions = $user->active_subscriptions->map(function ($subscription) {
            return [
                'id' => $subscription->id,
                'name' => $subscription->name,
                'description' => $subscription->description,
                'ending_date' => $subscription->ending_date,
                'gradiant_start' => $subscription->gradiant_start,
                'gradiant_end' => $subscription->gradiant_end,
                'bottom_color_at_start' => $subscription->bottom_color_at_start,
            ];
        })->toArray();

        return $this->sendResponse(['subscriptions' => $subscriptions]);
    }
}
