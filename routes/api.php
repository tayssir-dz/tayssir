<?php

use App\Enums\QuestionType;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ContentController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\EmailVerificationController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\UserController;
use App\Models\Question;
use Illuminate\Support\Facades\Route;


// MENNADOS PEDADAA
Route::prefix('v1')->group(function () {
    Route::get("/", function () {
        return response()->json([
            "message" => "Welcome to " . config('app.name') . " API",
            "version" => "1"
        ]);
    });


    Route::prefix('auth')->group(function () {
        Route::post("register", [AuthController::class, 'register'])
            ->summary("Register a new user")
            ->description("this endpoint takes the user name, email, phone number, and password and creates a new user, it also assigns the student role to the user, and returns the user data and a token");

        Route::post("login", [AuthController::class, 'login'])
            ->summary("Login a user")
            ->description("this endpoint takes the user email and password and logs the user in, it returns the user data and a token");

        Route::post("check-email", [AuthController::class, 'checkEmail'])
            ->summary("Check if email exists")
            ->description("this endpoint checks if an email exists in the system");


        Route::post("check-phone-number", [AuthController::class, 'checkPhoneNumber'])
            ->summary("Check if phone number exists")
            ->description("this endpoint checks if a phone number exists in the system");

        Route::post("logout", [AuthController::class, 'logout'])
            ->middleware(["auth:sanctum", "access"])
            ->summary("Logout a user")
            ->description("this endpoint logs the user out and deletes the user token");

        Route::post("refresh-token", [AuthController::class, 'refreshToken'])
            ->middleware(['auth:sanctum', 'refresh'])
            ->summary("Refresh user token")
            ->description("this endpoint deletes the current user token and returns a new one");
    });

    Route::prefix("email")->group(function () {
        Route::post("send-verification-mail", [EmailVerificationController::class, "sendVerificationMail"])
            ->summary("Send verification email")
            ->description("Send a verification email with OTP to verify user email");

        Route::post("/verify-otp", [EmailVerificationController::class, "verifyOtp"])
            ->summary("Verify OTP")
            ->description("this endpoint takes the user email and the otp and verifies the otp");

        Route::post("verify-email", [EmailVerificationController::class, "verifyEmail"])
            ->middleware(["auth:sanctum", "access"])
            ->summary("Verify user email")
            ->description("this endpoint takes the verification code and verifies the user email");

        Route::post("unverify-me", [EmailVerificationController::class, "unverifyMe"])
            ->middleware(["auth:sanctum", "access"])
            ->summary("Unverify user email (testing)")
            ->description("this endpoint unverifies the user email (testing)");
    });

    Route::prefix("user")->group(function () {
        Route::get("/", [UserController::class, "index"])
            ->middleware(["auth:sanctum", "access"])
            ->summary("Get user infos")
            ->description("this endpoint returns the user data");

        Route::put("/", [UserController::class, "updateUser"])
            ->middleware(["auth:sanctum", "access"])
            ->summary("Update user infos")
            ->description("this endpoint takes the user name, email, and phone number and updates the user data");

        Route::put("change-password", [UserController::class, "updatePassword"])
            ->middleware(["auth:sanctum", "access"])
            ->summary("Change user password")
            ->description("this endpoint takes the old password and the new password and changes the user password");
    });

    Route::prefix('forget-password')->group(function () {
        Route::post("/", [ForgotPasswordController::class, "forgotPassword"])
            ->summary("Forgot password")
            ->description("this endpoint takes the user email and sends a reset password mail");

        Route::post("/verify-otp", [ForgotPasswordController::class, "verifyOtp"])
            ->summary("Verify OTP")
            ->description("this endpoint takes the user email and the otp and verifies the otp");

        Route::post("/reset-password", [ForgotPasswordController::class, "resetPassword"])
            ->summary("Reset password")
            ->description("this endpoint takes the user email, the reset code, and the new password and resets the user password");
    });

    Route::prefix("subscriptions")->group(function () {
        Route::get("/", [SubscriptionController::class, "userSubscriptions"])
            ->middleware(["auth:sanctum", "access"])
            ->summary("Get user subscriptions")
            ->description("this returns an array of user subscriptions (id, name, description, ending_date)");

        Route::post("/redeem", [SubscriptionController::class, "redeem"])
            ->middleware(["auth:sanctum", "access"])
            ->summary("Redeem a subscription")
            ->description("this endpoint takes the code of the card and redeems it for the user, it errors if the card is already used by the user, if its used by another user, if the user already subscribed to the same subscription so there is no need to subscribe again");

        Route::post("/unsibscribe", [SubscriptionController::class, 'unsubscribe'])
            ->middleware(["auth:sanctum", "access"])
            ->summary("Unsubscribe from a subscription")
            ->description("this endpoint takes the subscription id and the user's password and unsubscribes the user from the subscription");
    });
    Route::prefix("content")->group(function () {
        Route::get("/", [ContentController::class, "getUserContent"])
            ->middleware(["auth:sanctum", "access"])
            ->summary("Get user content")
            ->description("This endpoint returns the content associated with the authenticated user.");
    });

    // Route::middleware('auth:sanctum')->group(function () {
    //     Route::post('/submit-chapter-answers', [ContentController::class, 'submitChapterAnswers']);
    // });

    Route::get('test', function () {
        // Use an anonymous class instance that uses the trait.
        $transformer = new class {
            use \App\Traits\InteractsWithContent;
        };

        $result = [];
        foreach (QuestionType::cases() as $type) {
            // Retrieve one example question for the current type.
            $question = Question::where('question_type', $type->value)->first();
            if ($question) {
                $result[$type->value] = $transformer->transformQuestion($question);
            }
        }
        return response()->json($result);
    });
});
