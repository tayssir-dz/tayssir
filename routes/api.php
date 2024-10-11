<?php

use App\Filament\Resources\MaterialResource;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CardsController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\Mail\EmailVerificationController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\UserController;
use App\Models\Question;
use Illuminate\Support\Facades\Route;


// MENNADOS
Route::prefix('v1')->group(function () {
    Route::get("/", function () {
        $questions = Question::all();
        return ["message" => "Welcome to " . env('APP_NAME') . " API", 'questions' => $questions];
    });


    Route::prefix("subscriptions")->group(function () {
        Route::get("/", [SubscriptionController::class, "userSubscriptions"])
            ->middleware("auth:sanctum")
            ->summary("Get user subscriptions")
            ->description("this returns an array of user subscriptions (id, name, description, ending_date)");

        Route::post("/redeem", [SubscriptionController::class, "redeem"])
            ->middleware("auth:sanctum")
            ->summary("Redeem a subscription")
            ->description("this endpoint takes the code of the card and redeems it for the user, it errors if the card is already used by the user, if its used by another user, if the user already subscribed to the same subscription so there is no need to subscribe again");

        Route::post("/unsibscribe", [SubscriptionController::class, 'unsubscribe'])
            ->middleware("auth:sanctum")
            ->summary("Unsubscribe from a subscription")
            ->description("this endpoint takes the subscription id and unsubscribes the user from the subscription");

    });

    Route::prefix('auth')->group(function () {
        Route::post("register", [AuthController::class, 'register'])
            ->summary("Register a new user")
            ->description("this endpoint takes the user name, email, phone number, and password and creates a new user, it also assigns the student role to the user, and returns the user data and a token");

        Route::post("login", [AuthController::class, 'login'])
            ->summary("Login a user")
            ->description("this endpoint takes the user email and password and logs the user in, it returns the user data and a token");

        Route::post("logout", [AuthController::class, 'logout'])
            ->middleware('auth:sanctum')
            ->summary("Logout a user")
            ->description("this endpoint logs the user out and deletes the user token");

        Route::post("refresh-token", [AuthController::class, 'refreshToken'])
            ->middleware('auth:sanctum')
            ->summary("Refresh user token")
            ->description("this endpoint deletes the current user token and returns a new one");
    });

    Route::prefix("email")->group(function () {
        Route::post("send-verification-mail", [EmailVerificationController::class, "sendVerificationMail"])
            ->middleware("auth:sanctum")
            ->summary("Send verification mail to user")
            ->description("this endpoint sends a verification mail to the user email");

        Route::post("verify-email", [EmailVerificationController::class, "verifyEmail"])
            ->middleware("auth:sanctum")
            ->summary("Verify user email")
            ->description("this endpoint takes the verification code and verifies the user email");

        Route::post("unverify-me", [EmailVerificationController::class, "unverifyMe"])
            ->middleware("auth:sanctum")
            ->summary("Unverify user email (testing)")
            ->description("this endpoint unverifies the user email (testing)");
    });

    Route::prefix('forget-password')->group(function () {
        Route::post("/", [ForgotPasswordController::class, "forgotPassword"])
            ->summary("Forgot password")
            ->description("this endpoint takes the user email and sends a reset password mail");

        Route::post("/reset-password", [ForgotPasswordController::class, "resetPassword"])
            ->summary("Reset password")
            ->description("this endpoint takes the user email, the reset code, and the new password and resets the user password");
    });

    Route::prefix("user")->group(function () {
        Route::get("/", [UserController::class, "index"])
            ->middleware(["auth:sanctum"])
            ->summary("Get user infos")
            ->description("this endpoint returns the user data");

        Route::put("/", [UserController::class, "update"])
            ->middleware("auth:sanctum")
            ->summary("Update user infos")
            ->description("this endpoint takes the user name, email, and phone number and updates the user data");

        Route::put("change-password", [UserController::class, "updatePassword"])
            ->middleware("auth:sanctum")
            ->summary("Change user password")
            ->description("this endpoint takes the old password and the new password and changes the user password");
    });

    Route::prefix("cards")->group(function () {
        Route::get("/", [CardsController::class, "index"])
            ->summary("Get a card by ID (testing)");

        Route::get("/{id}", [CardsController::class, "cardById"])
            ->summary("Get a card by ID (testing)");

        Route::post("/", [CardsController::class, "createCards"])
            ->summary("Create cards (testing)");

        Route::delete("/{id}", [CardsController::class, "deleteCard"])
            ->summary("Delete a card by ID (testing)");
    });

});

