<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\ResponseController;
use App\Http\Requests\API\EmailVerification\SendVerificationMailRequest;
use App\Http\Requests\API\EmailVerification\VerifyEmailRequest;
use App\Http\Requests\API\ForgotPassword\VerifyOtpRequest;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use Carbon\Carbon;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Validator;
use G4T\Swagger\Attributes\SwaggerSection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// #[SwaggerSection("This section is responsible for managing user email verification processes. It includes sending verification emails, verifying the user's email based on a code, and providing testing functionality to unverify emails. It helps maintain a verified email system, ensuring only verified users access specific features.")]
class EmailVerificationController extends BaseController
{
    public function sendVerificationMail(SendVerificationMailRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user->email_verified_at !== null) {
            return $this->sendError(__('response.email_already_verified'));
        }
        $verification_otp = (new Otp)->generate($user->email, 'numeric', 6, 10);
        if (!$verification_otp->status) {
            return $this->sendError(__("response.failed_to_generate_otp"));
        }
        Mail::to($user->email)->send(new EmailVerificationMail([
            'otp' => $verification_otp->token,
            'name' => $user->name
        ]));
        return $this->sendResponse(message: __("response.email_sent_successfully"));
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        $user = $request->user();
        if ($user->email_verified_at !== null) {
            return $this->sendError(__("response.email_already_verified"));
        }
        $obj = (new Otp)->validate($user->email, $request->otp);
        if ($obj->status === false) {
            return $this->sendError(__("response.invalid_otp"));
        }
        $user->email_verified_at = now();
        $user->save();
        return $this->sendResponse(["user" => ResponseController::userRes($user)], __("response.email_verified_successfully"));
    }

    public function unverifyMe(Request $request)
    {
        $user = $request->user();
        if ($user->email_verified_at === null) {
            return $this->sendError(__("response.email_already_unverified"));
        }
        $user->email_verified_at = null;
        $user->save();
        return response()->json(["message" => __("response.user_unverified_successfully")]);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $otpRecord = DB::table('otps')
            ->where('identifier', $request->email)
            ->where('token', $request->otp)
            ->where('valid', 1)
            ->first();
        if (!$otpRecord) {
            return $this->sendError(__("response.invalid_otp"));
        }

        $expiry = Carbon::parse($otpRecord->created_at)->addMinutes($otpRecord->validity);
        if ($expiry->isPast()) {
            return $this->sendError(__("response.otp_has_expired"));
        }
        return $this->sendResponse([], __("response.otp_is_valid"));
    }
}
