<?php

namespace App\Http\Controllers\API;

use App\Enums\TokenAbility;
use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\ResponseController;
use App\Http\Requests\API\Auth\CheckEmailRequest;
use App\Http\Requests\API\Auth\CheckPhoneNumberRequest;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Mail\WelcomeMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Validator;
use G4T\Swagger\Attributes\SwaggerSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

// #[SwaggerSection('This section manages all authentication-related actions such as user registration, login, logout, and token refresh. It ensures secure authentication processes, handling both token-based and user-based operations for registering and logging users in and out of the system.')]
class AuthController extends BaseController
{
    public function register(RegisterRequest $request)
    {
        $input = $request->all();

        $input['password'] = bcrypt($input['password']);
        $user = User::create([
            'email' => $input['email'],
            'name' => $input['name'],
            'phone_number' => $input['phone_number'],
            'password' => $input['password'],
            'wilaya_id' => $input['wilaya_id'] ?? null,
            'commune_id' => $input['commune_id'] ?? null,
            'division_id' => $input['division_id'] ?? null,
            'age' => $input['age'] ?? null,
        ]);
        $role = Role::firstOrCreate(['name' => 'student']);
        $user->assignRole($role);

        // Send welcome email
        Mail::to($user->email)->send(new WelcomeMail([
            'name' => $user->name
        ]));

        // $token = $user->createToken($input["device_name"])->plainTextToken;
        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.access_token_expiration')));
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::REFRESH_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.refresh_token_expiration')));

        return $this->sendResponse([
            'token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            // 'user' => ResponseController::userRes($user),
        ], __("response.user_register_successfully"));
    }
    public function login(LoginRequest $request)
    {
        $input = $request->all();

        if (Auth::attempt(['email' => $input["email"], 'password' => $input["password"]])) {
            /** @var User $user */
            $user = Auth::user();
            $user->tokens()->delete();

            $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.access_token_expiration')));
            $refreshToken = $user->createToken('refresh_token', [TokenAbility::REFRESH_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.refresh_token_expiration')));

            return $this->sendResponse(
                [
                    'token' => $accessToken->plainTextToken,
                    'refresh_token' => $refreshToken->plainTextToken,
                    // 'user' => ResponseController::userRes($user),
                ],
                __("response.user_login_successfully")
            );
        } else {
            return $this->sendError(__("response.unauthorised"), ['error' => __("response.wrong_email_or_password")]);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->sendResponse([], __("response.user_logout_successfully"));
    }

    public function refreshToken(Request $request)
    {
        // delete the old token (only the access_token)
        $request->user()->tokens()->where('name', 'access_token')->delete();
        $accessToken = $request->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.access_token_expiration')));

        return $this->sendResponse([
            "token" => $accessToken->plainTextToken,
        ], __("response.token_generated_successfully"));
    }

    public function checkEmail(CheckEmailRequest $request)
    {
        $exists = User::where('email', $request->email)->exists();

        return $this->sendResponse([
            'message' => $exists ? __('response.email_exists') : __('response.email_not_exists'),
            'exists' => $exists
        ]);
    }

    public function checkPhoneNumber(CheckPhoneNumberRequest $request)
    {
        $exists = User::where('phone_number', $request->phone_number)->exists();

        return $this->sendResponse([
            'message' => $exists ? __('response.phone_exists') : __('response.phone_not_exists'),
            'exists' => $exists
        ]);
    }
}
