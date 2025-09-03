<?php

namespace App\Services\Purchase;

use App\Enums\Purchase\PaymentStatus;
use App\Enums\Purchase\PaymentType;
use App\Models\Payment;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ManualPaymentService
{
    public function __construct(protected PriceCheckerService $priceCheckerService) {}

    /**
     * Compute promoter margin based on the final payable price (after all discounts).
     * Returns an array with [percentage, amount].
     */
    protected function computePromoterMargin(array $pricingDetails, ?PromoCode $promoCode): array
    {
        $percentage = (float) ($promoCode?->promoter_margin ?? 0.0);
        // Normalize percentage bounds (0 - 100)
        if ($percentage < 0) {
            $percentage = 0.0;
        } elseif ($percentage > 100) {
            $percentage = 100.0;
        }

        // Base is the final price after all discounts (original - subscription - promo code)
        $finalPrice = (float) $pricingDetails['final_price'];
        if ($finalPrice < 0) {
            $finalPrice = 0.0; // Safety guard
        }

        $amount = round($finalPrice * ($percentage / 100), 2);

        return [round($percentage, 2), $amount];
    }

    /**
     * Initiates a manual payment request for a user.
     *
     * @param User $user The authenticated user making the payment.
     * @param int $subscriptionId The ID of the subscription being purchased.
     * @param ?string $promoCodeCode An optional promo code.
     * @param UploadedFile $attachment The payment proof attachment.
     * @return Payment The created manual payment record.
     * @throws \InvalidArgumentException If promo code is invalid or inactive (though validation should catch most).
     */
    public function initiatePayment(User $user, int $subscriptionId, ?string $promoCodeCode, UploadedFile $attachment): Payment
    {
        return DB::transaction(function () use ($user, $subscriptionId, $promoCodeCode, $attachment) {
            // 1. Get pricing details using PriceCheckerService (do not modify this service)
            $pricingDetails = $this->priceCheckerService->checkPrice(
                subscriptionId: $subscriptionId,
                promoCodeCode: $promoCodeCode
            );

            // 2. Determine promo code (PriceCheckerService does not return its id)
            $promoCode = null;
            if ($promoCodeCode) {
                $promoCode = PromoCode::where('code', $promoCodeCode)->first(); // Already validated inside PriceCheckerService
            }

            $promoCodeId = $promoCode?->id;

            // 3. Calculate promoter margin (based on final payable price after discounts)
            [$promoterMarginPercentage, $promoterMarginAmount] = $this->computePromoterMargin($pricingDetails, $promoCode);


            // 4. Create the Payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscriptionId,
                'promo_code_id' => $promoCodeId,
                'status' => PaymentStatus::PENDING,
                'payment_type' =>  PaymentType::MANUAL,
                'price' => $pricingDetails['original_price'],
                'discount_percentage' => $pricingDetails['subscription_discount']['percentage'],
                'discount_amount' => $pricingDetails['subscription_discount']['amount'],
                'promocode_percentage' => $pricingDetails['promocode_discount']['percentage'],
                'promocode_amount' => $pricingDetails['promocode_discount']['amount'],
                'combined_discount_percentage' => $pricingDetails['combined_discount']['percentage'],
                'combined_discount_amount' => $pricingDetails['combined_discount']['amount'],
                'final_price' => $pricingDetails['final_price'],
                'promoter_margin_percentage' => round($promoterMarginPercentage, 2),
                'promoter_margin_amount' => round($promoterMarginAmount, 2),
            ]);

            // 5. Attach the file using Spatie Media Library (collection name: attachment)
            $payment->addMedia($attachment)->toMediaCollection('attachment');

            // Refresh to get the attachment URL if needed immediately
            $payment->refresh();

            return $payment;
        });
    }
}
