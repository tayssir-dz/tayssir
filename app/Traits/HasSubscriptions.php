<?php

namespace App\Traits;

use App\Models\SubscriptionCard;
use App\Models\Subscription;
use App\Models\Unit;
use App\Models\Material;

trait HasSubscriptions
{
    public function subscriptionCards()
    {
        return $this->hasMany(SubscriptionCard::class);
    }
    public function subscription_cards()
    {
        return $this->hasMany(SubscriptionCard::class)
            ->where('redeemed_at', '!=', null)
            ->whereHas('subscription', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('ending_date')
                        ->orWhere('ending_date', '>', now());
                });
            })
            ->with('subscription')
            ->latest('redeemed_at');
    }

    public function getSubscriptionsAttribute()
    {
        $subscriptions = $this->subscription_cards
            ->map(fn($card) => $card->subscription)
            ->filter();

        $guestSubscription = Subscription::find(Subscription::GUEST_ID);
        if ($guestSubscription && !$subscriptions->contains('id', Subscription::GUEST_ID)) {
            $subscriptions->push($guestSubscription);
        }

        return $subscriptions->unique('id')->values();
    }

    public function getActiveSubscriptionsAttribute()
    {
        $subscriptions = $this->subscription_cards
            ->map(fn($card) => $card->subscription)
            ->filter();

        if ($subscriptions->isEmpty()) {
            // If no subscriptions, return guest only
            return collect([Subscription::find(Subscription::GUEST_ID)])
                ->filter();
        }

        // If has subscriptions, return them without guest
        return $subscriptions->unique('id')->values();
    }

    public function getAccessibleUnitsAttribute()
    {
        return Unit::whereHas('subscriptions', function ($query) {
            $query->whereIn('subscriptions.id', $this->subscriptions->pluck('id'));
        })->get()->toArray();
    }

    public function getAccessibleMaterialsAttribute()
    {
        return $this->division->materials()
            ->whereHas('units', function ($query) {
                $query->whereHas('subscriptions', function ($subQuery) {
                    $subQuery->whereIn('subscriptions.id', $this->subscriptions->pluck('id'));
                });
            })
            ->with(['units' => function ($query) {
                $query->whereHas('subscriptions', function ($subQuery) {
                    $subQuery->whereIn('subscriptions.id', $this->subscriptions->pluck('id'));
                })->with(['chapters' => function ($chapterQuery) {
                    $chapterQuery->whereHas('subscriptions', function ($subQuery) {
                        $subQuery->whereIn('subscriptions.id', $this->subscriptions->pluck('id'));
                    })->with('questions');
                }]);
            }])
            ->get()
            ->toArray();
    }
}
