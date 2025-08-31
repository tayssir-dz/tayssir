<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\V2\CheckPriceRequest;
use App\Http\Requests\API\V2\ManualPaymentRequest;
use App\Services\Notification\AdminNotifications;
use App\Services\Purchase\ManualPaymentService;
use App\Services\Purchase\PriceCheckerService;
use Dedoc\Scramble\Attributes\Group;

#[Group('Subscription Purchase APIs', weight: 2)]
class PurchaseControllerV2 extends BaseController
{

    // Constructor for dependency injection
    public function __construct(
        protected PriceCheckerService $priceCheckerService,
        protected ManualPaymentService $manualPaymentService // Inject the new service
    ) {}

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
        $data = $this->priceCheckerService->checkPrice(
            subscriptionId: $request->integer('subscription_id'),
            promoCodeCode: $request->filled('promocode') ? $request->string('promocode')->toString() : null,
        );

        return $this->sendResponse($data);
    }


    /**
     * Initiate a manual payment request.
     *
     * This endpoint allows an authenticated user to initiate a manual payment for a subscription.
     * It requires a subscription ID, an optional promo code, and a mandatory attachment (e.g., proof of payment).
     * The system will calculate the final price with any applicable discounts and create a pending manual payment record.
     * An administrator will then review and approve/reject the payment.
     *
     * @param ManualPaymentRequest $request
     * @return JsonResponse
     */
    public function initiateManualPayment(ManualPaymentRequest $request)
    {
        $current_user = $request->user();
        $manualPayment = $this->manualPaymentService->initiatePayment(
            user: $current_user, // Get the authenticated user
            subscriptionId: $request->integer('subscription_id'),
            promoCodeCode: $request->string('promocode')?->toString(),
            attachment: $request->file('attachment')
        );

        $data = $manualPayment->toArray();
        // $data['attachment_url'] = $manualPayment->attachment_url;

        AdminNotifications::newManualPayment($current_user, $manualPayment->subscription->name, "https://google.com");

        return $this->sendResponse($data, 'Manual payment request initiated successfully and is pending review.');
    }
}
