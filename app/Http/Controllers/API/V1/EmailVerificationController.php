<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\API\ResponseController;
use App\Http\Requests\API\EmailVerification\SendVerificationMailRequest;
use App\Http\Requests\API\EmailVerification\VerifyEmailRequest;
use App\Http\Requests\API\ForgotPassword\VerifyOtpRequest;
use App\Notifications\EmailVerificationNotification;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\Group;
use G4T\Swagger\Attributes\SwaggerSection;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// #[SwaggerSection("This section is responsible for managing user email verification processes. It includes sending verification emails, verifying the user's email based on a code, and providing testing functionality to unverify emails. It helps maintain a verified email system, ensuring only verified users access specific features.")]
#[Group('Email Verification APIs', weight: 2)]
class EmailVerificationController extends BaseController
{
    /**
     * Send verification email.
     *
     * Send a verification email with OTP to verify user email.
     */
    public function sendVerificationMail(SendVerificationMailRequest $request)
    {
        $user = $request->user();
        if ($user->email_verified_at !== null) {
            return $this->sendError(__('response.email_already_verified'));
        }
        $verification_otp = (new Otp)->generate($user->email, 'numeric', 6, 10);
        if (! $verification_otp->status) {
            return $this->sendError(__('response.failed_to_generate_otp'));
        }
        $user->notify(new EmailVerificationNotification($verification_otp->token));

        // return $this->sendResponse(["otp" => $verification_otp->token], message: __('response.email_sent_successfully'));
        return $this->sendResponse(message: __('response.email_sent_successfully'));
    }

    /**
     * Verify user email.
     *
     * This endpoint takes the verification code and verifies the user email.
     */
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $user = $request->user();
        if ($user->email_verified_at !== null) {
            return $this->sendError(__('response.email_already_verified'));
        }
        $obj = (new Otp)->validate($user->email, $request->otp);
        if ($obj->status === false) {
            return $this->sendError(__('response.invalid_otp'));
        }
        $user->email_verified_at = now();
        $user->save();

        $user->notify(new WelcomeNotification($user->name));

        return $this->sendResponse(['user' => ResponseController::userRes($user)], __('response.email_verified_successfully'));
    }

    /**
     * Unverify user email (testing).
     *
     * This endpoint unverifies the user email (testing).
     */
    public function unverifyMe(Request $request)
    {
        $user = $request->user();
        if ($user->email_verified_at === null) {
            return $this->sendError(__('response.email_already_unverified'));
        }
        $user->email_verified_at = null;
        $user->save();

        return response()->json(['message' => __('response.user_unverified_successfully')]);
    }

    /**
     * Verify OTP.
     *
     * This endpoint takes the user email and the otp and verifies the otp.
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $otpRecord = DB::table('otps')
            ->where('identifier', $request->email)
            ->where('token', $request->otp)
            ->where('valid', 1)
            ->first();
        if (! $otpRecord) {
            return $this->sendError(__('response.invalid_otp'));
        }

        $expiry = Carbon::parse($otpRecord->created_at)->addMinutes($otpRecord->validity);
        if ($expiry->isPast()) {
            return $this->sendError(__('response.otp_has_expired'));
        }

        return $this->sendResponse(message: __('response.otp_is_valid'));
    }
}
