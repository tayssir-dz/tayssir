<?php

namespace App\Services\Purchase;

use App\Enums\Purchase\PaymentStatus;
use App\Enums\Purchase\PaymentType;
use App\Models\Payment;
use App\Models\PromoCode;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\Purchase\ChargilyPaymentFailed;
use App\Notifications\Purchase\ChargilyPaymentSucceeded;
use Chargily\ChargilyPay\Auth\Credentials;
use Chargily\ChargilyPay\ChargilyPay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChargilyPaymentService
{
    public function __construct(protected PriceCheckerService $priceCheckerService) {}

    protected function client(): ChargilyPay
    {
        return new ChargilyPay(new Credentials([
            'mode' => config('chargily.mode'),
            'public' => config('chargily.public'),
            'secret' => config('chargily.secret'),
        ]));
    }

    /**
     * Start a Chargily checkout and create a Payment record (status pending).
     */
    public function createCheckout(User $user, int $subscriptionId, ?string $promoCodeCode = null, string $locale = 'ar')
    {
        $subscription = Subscription::query()->findOrFail($subscriptionId);

        $pricing = $this->priceCheckerService->checkPrice($subscriptionId, $promoCodeCode);
        $promoCode = $promoCodeCode ? PromoCode::where('code', $promoCodeCode)->first() : null;

        [$promoterMarginPercentage, $promoterMarginAmount] = $this->computePromoterMargin($pricing, $promoCode);

        [$payment, $checkout] = DB::transaction(function () use ($user, $subscriptionId, $promoCode, $pricing, $promoterMarginPercentage, $promoterMarginAmount, $locale) {
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscriptionId,
                'promo_code_id' => $promoCode?->id,
                'status' => PaymentStatus::PENDING,
                'payment_type' => PaymentType::CHARGILY,
                'price' => $pricing['original_price'],
                'discount_percentage' => $pricing['subscription_discount']['percentage'],
                'discount_amount' => $pricing['subscription_discount']['amount'],
                'promocode_percentage' => $pricing['promocode_discount']['percentage'],
                'promocode_amount' => $pricing['promocode_discount']['amount'],
                'combined_discount_percentage' => $pricing['combined_discount']['percentage'],
                'combined_discount_amount' => $pricing['combined_discount']['amount'],
                'final_price' => $pricing['final_price'],
                'promoter_margin_percentage' => $promoterMarginPercentage,
                'promoter_margin_amount' => $promoterMarginAmount,
                'metadata' => [
                    'initiated_via' => 'api',
                ],
            ]);

            $checkout = $this->client()->checkouts()->create([
                'metadata' => [
                    'payment_id' => $payment->id,
                    'subscription_id' => $payment->subscription_id,
                    'subscription_name' => $payment->subscription->name,
                    'subscription_description' => $payment->subscription->description,
                    'price' => $pricing['original_price'],
                    'discount_percentage' => $pricing['subscription_discount']['percentage'],
                    'promocode_percentage' => $pricing['promocode_discount']['percentage'],
                    'combined_discount_percentage' => $pricing['combined_discount']['percentage'],
                    'final_price' => $pricing['final_price'],
                    'promoter_margin_percentage' => $promoterMarginPercentage,
                    'promoter_margin_amount' => $promoterMarginAmount,
                ],
                'locale' => $locale,
                'amount' => (string) $payment->final_price, // Chargily expects string
                'currency' => 'dzd', // assuming DZD; could map from subscription if needed
                'description' => 'Subscription purchase #' . $payment->id,
                'success_url' => route('chargilypay.back'),
                'failure_url' => route('chargilypay.back'),
                'webhook_endpoint' => route('chargilypay.webhook_endpoint'),
            ]);
            $payment->metadata = array_merge($payment->metadata ?? [], [
                'chargily_checkout_id' => $checkout->getId(),
            ]);
            $payment->save();
            return [$payment, $checkout];
        });
        return [$payment, $checkout];
    }

    /**
     * Handle webhook payload (already validated signature by SDK) and update payment.
     */
    public function handleWebhook(): array
    {
        $webhook = $this->client()->webhook()->get();
        if (!$webhook) {
            return ['ok' => false, 'message' => 'No webhook'];
        }
        $checkout = $webhook->getData();
        if (!$checkout || !$checkout instanceof \Chargily\ChargilyPay\Elements\CheckoutElement) {
            return ['ok' => false, 'message' => 'Invalid data'];
        }
        $metadata = $checkout->getMetadata();
        $paymentId = $metadata['payment_id'] ?? null;
        if (!$paymentId) {
            return ['ok' => false, 'message' => 'Missing payment id'];
        }
        $payment = Payment::find($paymentId);
        if (!$payment) {
            return ['ok' => false, 'message' => 'Payment not found'];
        }
        $status = $checkout->getStatus();
        if ($status === 'paid') {
            $payment->status = PaymentStatus::SUCCEEDED;
            $success = SubscriptionActivationService::ActivateSubscriptionForUser($payment->subscription_id, $payment->user_id);
            if ($success) {
                $payment->user->notify(new ChargilyPaymentSucceeded($payment->subscription->name));
            } else {
                $payment->user->notify(new ChargilyPaymentFailed($payment->subscription->name));
            }
        } elseif (in_array($status, ['failed', 'canceled'])) {
            $payment->status = PaymentStatus::FAILED;
            $payment->user->notify(new ChargilyPaymentFailed($payment->subscription->name));
        }
        $payment->metadata = array_merge($payment->metadata ?? [], [
            'chargily_checkout_id' => $checkout->getId(),
            'chargily_status' => $status,
        ]);
        $payment->save();
        return ['ok' => true, 'status' => $status];
    }

    protected function computePromoterMargin(array $pricingDetails, ?PromoCode $promoCode): array
    {
        $percentage = (float) ($promoCode?->promoter_margin ?? 0.0);
        $percentage = max(0.0, min(100.0, $percentage));
        $finalPrice = max(0.0, (float) $pricingDetails['final_price']);
        $amount = round($finalPrice * ($percentage / 100), 2);
        return [round($percentage, 2), $amount];
    }
}
