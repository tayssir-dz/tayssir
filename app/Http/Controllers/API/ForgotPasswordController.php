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

class ForgotPasswordController extends BaseController
{
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $forgot_password_otp = (new Otp)->generate($user->email, 'numeric', 6, 10);
            if (!$forgot_password_otp->status) {
                return $this->sendError("Failed to generate OTP");
            }
            // Mail::to($user->email)->send(new ForgotPasswordMail([
            //     'otp' => $forgot_password_otp->token,
            //     'name' => $user->name
            // ]));
        }
        return $this->sendResponse([
            'otp' => $forgot_password_otp->token,
        ], "If the email exists, the OTP has been sent to the email (for testing purposes, the otp is also returned)");
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->sendError("User not found");
        }

        $obj = (new Otp)->validate($user->email, $request->otp);
        if ($obj->status === false) {
            return $this->sendError("Invalid OTP");
        }
        $user->password = bcrypt($request->new_password);
        $user->save();

        return $this->sendResponse([], "Password reset successfully");
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $otpRecord = \DB::table('otps')
            ->where('identifier', $request->email)
            ->where('token', $request->otp)
            ->where('valid', 1)
            ->first();
        if (!$otpRecord) {
            return $this->sendError("Invalid OTP");
        }

        $expiry = Carbon::parse($otpRecord->created_at)->addMinutes($otpRecord->validity);
        if ($expiry->isPast()) {
            return $this->sendError("OTP has expired");
        }

        return $this->sendResponse([], "OTP is valid");
    }
}
