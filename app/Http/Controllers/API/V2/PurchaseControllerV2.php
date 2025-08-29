<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\V2\CheckPriceRequest;
use App\Services\Purchase\PriceCheckerService;
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
    public function checkPrice(CheckPriceRequest $request, PriceCheckerService $service)
    {
        $data = $service->checkPrice(
            subscriptionId: $request->integer('subscription_id'),
            promoCodeCode: $request->filled('promocode') ? $request->string('promocode')->toString() : null,
        );

        return $this->sendResponse($data);
    }
}
