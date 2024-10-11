<?php

namespace App\Http\Controllers\API\Mail;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\ResponseController;
use App\Http\Requests\API\EmailVerification\VerifyEmailRequest;
use App\Mail\EmailVerificationMail;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Mail;
use Validator;

class EmailVerificationController extends BaseController
{
    public function sendVerificationMail(Request $request)
    {
        $user = $request->user();
        if ($user->email_verified_at !== null) {
            return $this->sendError("Email already verified");
        }
        $verification_otp = (new Otp)->generate($user->email, 'numeric', 6, 10);
        if (!$verification_otp->status) {
            return $this->sendError("Failed to generate OTP");
        }
        Mail::to($user->email)->send(new EmailVerificationMail([
            'otp' => $verification_otp->token,
            'name' => $user->name
        ]));
        return $this->sendResponse([
            "otp" => $verification_otp->token,
        ], "Email sent successfully (for testing purposes, the otp is also returned)");
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        $user = $request->user();
        if ($user->email_verified_at !== null) {
            return $this->sendError("Email already verified");
        }
        $obj = (new Otp)->validate($user->email, $request->otp);
        if ($obj->status === false) {
            return $this->sendError("Invalid OTP");
        }
        $user->email_verified_at = now();
        $user->save();
        return $this->sendResponse(["user" => ResponseController::userRes($user)], "Email verified successfully");
    }

    public function unverifyMe(Request $request)
    {
        $user = $request->user();
        if ($user->email_verified_at === null) {
            return $this->sendError("Email already unverified");
        }
        $user->email_verified_at = null;
        $user->save();
        return response()->json(["message" => "User unverified successfully"]);
    }
}
