<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\ForgotPassword\ForgotPasswordRequest;
use App\Http\Requests\API\ForgotPassword\ResetPasswordRequest;
use App\Http\Requests\API\ForgotPassword\VerifyOtpRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Mail;
use G4T\Swagger\Attributes\SwaggerSection;

// #[SwaggerSection("This section manages the password recovery process. It handles sending password reset emails, verifying OTPs, and resetting passwords through a secure multi-step process, ensuring that users can regain access to their accounts while maintaining security.")]
class ForgotPasswordController extends BaseController
{
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $forgot_password_otp = (new Otp)->generate($user->email, 'numeric', 6, 10);
            if (!$forgot_password_otp->status) {
                return $this->sendError(__("response.failed_to_generate_otp"));
            }
            Mail::to($user->email)->send(new ForgotPasswordMail([
                'otp' => $forgot_password_otp->token,
                'name' => $user->name
            ]));
        }
        return $this->sendResponse(message: __("response.email_sent_successfully"));
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->sendError(__("response.user_not_found"));
        }

        $obj = (new Otp)->validate($user->email, $request->otp);
        if ($obj->status === false) {
            return $this->sendError(__("response.invalid_otp"));
        }
        $user->password = bcrypt($request->new_password);
        $user->save();

        return $this->sendResponse([], __("response.password_reset_successfully"));
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $otpRecord = \DB::table('otps')
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
