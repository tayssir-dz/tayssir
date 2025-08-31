<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\ChangeEmail\ChangeEmailRequest;
use App\Http\Requests\API\ChangeEmail\VerifyChangeEmailRequest;
use App\Notifications\ChangeEmailVerificationNotification;
use Dedoc\Scramble\Attributes\Group;
use Ichtrojan\Otp\Otp;

#[Group('Email Change APIs', weight: 5)]
class ChangeEmailController extends BaseController
{
    /**
     * Request email change.
     *
     * This endpoint takes a new email address and sends a verification OTP to it.
     */
    public function changeEmail(ChangeEmailRequest $request)
    {
        $user = $request->user();

        // Store the new email in the new_email field
        $user->new_email = $request->new_email;
        $user->save();

        // Generate OTP
        $verification_otp = (new Otp)->generate($user->new_email, 'numeric', 6, 10);
        if (! $verification_otp->status) {
            return $this->sendError(__('response.failed_to_generate_otp'));
        }

        // Notify user (routes to new_email via routeNotificationForMail)
        $user->notify(new ChangeEmailVerificationNotification($verification_otp->token));

        return $this->sendResponse(message: __('response.email_change_verification_sent'));
    }

    /**
     * Verify email change.
     *
     * This endpoint verifies the OTP sent to the new email and completes the email change process.
     */
    public function verifyChangeEmail(VerifyChangeEmailRequest $request)
    {
        $user = $request->user();

        if (! $user->new_email) {
            return $this->sendError(__('response.no_email_change_requested'));
        }

        $obj = (new Otp)->validate($user->new_email, $request->otp);
        if ($obj->status === false) {
            return $this->sendError(__('response.invalid_otp'));
        }

        // Update user email
        $old_email = $user->email;
        $user->email = $user->new_email;
        $user->new_email = null;
        $user->email_verified_at = now(); // Mark as verified since we've verified it
        $user->save();

        return $this->sendResponse([
            'old_email' => $old_email,
            'new_email' => $user->email,
        ], __('response.email_changed_successfully'));
    }
}
