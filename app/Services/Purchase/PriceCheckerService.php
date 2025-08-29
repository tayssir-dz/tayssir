<?php

namespace App\Services\Purchase;

use App\Models\PromoCode;
use App\Models\Subscription;

class PriceCheckerService
{
    /**
     * Core business logic to calculate pricing details for a subscription with optional promo code.
     * Returns normalized price/discount values (prices divided by 100 assuming stored as cents).
     *
     * @throws \InvalidArgumentException when promo code is invalid or inactive.
     */
    public function checkPrice(int $subscriptionId, ?string $promoCodeCode = null): array
    {
        $subscription = Subscription::with('discounts')->findOrFail($subscriptionId);

        $originalPrice = (float) $subscription->price;

        $subscriptionDiscountPercentage = (float) ($subscription->discount_percentage ?? 0.0);
        $subscriptionDiscountAmount = (float) ($subscription->discount_amount ?? 0.0);

        $promoCodeDiscountPercentage = 0.0;
        $promoCodeDiscountAmount = 0.0;

        if ($promoCodeCode) {
            $promoCode = PromoCode::where('code', $promoCodeCode)->first();
            if (! $promoCode) {
                throw new \InvalidArgumentException('The selected promocode is invalid.');
            }
            if (! $promoCode->is_active) {
                throw new \InvalidArgumentException('The promo code is not active.');
            }
            $promoCodeDiscountPercentage = (float) ($promoCode->student_discount ?? 0.0);
            if ($promoCodeDiscountPercentage < 0) {
                $promoCodeDiscountPercentage = 0.0;
            }
            $promoCodeDiscountAmount = $originalPrice * ($promoCodeDiscountPercentage / 100);
        }

        $combinedDiscountPercentage = $subscriptionDiscountPercentage + $promoCodeDiscountPercentage;
        $combinedDiscountAmount = $originalPrice * ($combinedDiscountPercentage / 100);

        return [
            'original_price' => $originalPrice / 100,
            'subscription_discount' => [
                'percentage' => $subscriptionDiscountPercentage,
                'amount' => $subscriptionDiscountAmount / 100,
            ],
            'promocode_discount' => [
                'percentage' => $promoCodeDiscountPercentage,
                'amount' => $promoCodeDiscountAmount / 100,
            ],
            'combined_discount' => [
                'percentage' => $combinedDiscountPercentage,
                'amount' => $combinedDiscountAmount / 100,
            ],
            'final_price' => ($originalPrice - $combinedDiscountAmount) / 100,
        ];
    }
}
