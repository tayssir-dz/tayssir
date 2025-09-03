<?php

namespace App\Http\Controllers;

use App\Services\Purchase\ChargilyPaymentService;
use Illuminate\Http\Request;

class ChargilyPayController extends \App\Http\Controllers\API\BaseController
{
    public function __construct(protected ChargilyPaymentService $chargilyPaymentService) {}

    public function redirect(Request $request)
    {
        $user = $request->user();
        $subscriptionId = (int) $request->input('subscription_id');
        $promo = $request->input('promocode');
        [$payment, $checkout] = $this->chargilyPaymentService->createCheckout($user, $subscriptionId, $promo);
        return redirect($checkout->getUrl());
    }

    public function back(Request $request)
    {
        return $this->sendResponse(message: 'Payment processing pending verification. Check status later.');
    }

    public function webhook()
    {
        $result = $this->chargilyPaymentService->handleWebhook();
        if ($result['ok']) {
            return $this->sendResponse(message: $result['message']);
            // return response()->json(['status' => true, 'data' => $result]);
        }
        return $this->sendError($result['message'], code: 403);
        // return response()->json(['status' => false, 'message' => $result['message']], 403);
    }
}
