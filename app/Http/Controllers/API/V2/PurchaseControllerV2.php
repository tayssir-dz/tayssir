<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\V2\CheckPriceRequest;
use App\Models\PromoCode;
use App\Models\Subscription;
use Dedoc\Scramble\Attributes\Group;

#[Group('Subscription Purchase APIs', weight: 2)]
class PurchaseControllerV2 extends BaseController
{

    /**
     * Check subscription price (with discounts & optional promo code).
     *
     * This endpoint returns pricing details for a given subscription. It includes:
     * - Original subscription price.
     * - Subscription discount percentage & amount (aggregated active discounts on the subscription itself).
     * - Promo code discount percentage & amount (only if a valid, active promo code is provided and within its date range).
     * - Combined discount percentage (sum) & amount (applied on original price) when both subscription & promo code discounts are present.
     * It validates the provided subscription and optional promo code. A promo code is only applied if it is active (between start_date and end_date inclusive). Returns zero values for any discount segment that does not apply.
     */
    public function checkPrice(CheckPriceRequest $request)
    {
        $subscription = Subscription::with('discounts')->findOrFail($request->integer('subscription_id'));

        $originalPrice = (float) $subscription->price;

        // Subscription discounts (already aggregated via accessors in HasDiscounts trait)
        $subscriptionDiscountPercentage = (float) ($subscription->discount_percentage ?? 0.0); // accessor
        $subscriptionDiscountAmount = (float) ($subscription->discount_amount ?? 0.0); // accessor

        // Promo code handling
        $promoCodeInput = $request->string('promocode')->toString();
        $promoCode = null;
        $promoCodeDiscountPercentage = 0.0;
        $promoCodeDiscountAmount = 0.0;

        if ($promoCodeInput !== '') {
            /** @var PromoCode $promoCode */
            $promoCode = PromoCode::where('code', $promoCodeInput)->first();

            if (! $promoCode) {
                return $this->sendValidationError(['promocode' => ['The selected promocode is invalid.']]);
            }

            if (! $promoCode->is_active) {
                return $this->sendValidationError(['promocode' => ['The promo code is not active.']]);
            }

            // Assume student_discount represents the discount percentage for the user.
            $promoCodeDiscountPercentage = (float) ($promoCode->student_discount ?? 0.0);
            if ($promoCodeDiscountPercentage < 0) {
                $promoCodeDiscountPercentage = 0.0;
            }
            $promoCodeDiscountAmount = $originalPrice * ($promoCodeDiscountPercentage / 100);
        }

        // Combined
        $combinedDiscountPercentage = $subscriptionDiscountPercentage + $promoCodeDiscountPercentage;
        $combinedDiscountAmount = $originalPrice * ($combinedDiscountPercentage / 100);

        return $this->sendResponse([
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
        ]);
    }
}
