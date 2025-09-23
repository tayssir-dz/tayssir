<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\TokenAbility;
use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\API\ResponseController;
use App\Http\Requests\API\Auth\CheckEmailRequest;
use App\Http\Requests\API\Auth\CheckPhoneNumberRequest;
use App\Http\Requests\API\Auth\GoogleLoginRequest;
use App\Http\Requests\API\Auth\GoogleRegisterRequest;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Notifications\WelcomeNotification;
use App\Models\User;
use App\Services\Auth\GoogleAuthService;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\Group;
use G4T\Swagger\Attributes\SwaggerSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

#[Group('Authentication APIs', weight: 1)]
class AuthController extends BaseController
{
    /**
     * Register with Google.
     *
     * Registers (or updates) a user using a Google ID token and returns Sanctum access & refresh tokens.
     */
    public function googleRegister(GoogleRegisterRequest $request, GoogleAuthService $google)
    {
        $payload = $google->verifyIdToken($request->id_token);
        if (! $payload) {
            return $this->sendError(__('response.unauthorised'), ['error' => 'Invalid Google ID token'], 401);
        }
        $googleEmail = $payload['email'] ?? null;
        $googleId = $payload['sub'] ?? null;
        $avatarUrl = $payload['picture'] ?? null;
        if (! $googleEmail || ! $googleId) {
            return $this->sendError(__('response.unauthorised'), ['error' => 'Google token missing email or sub'], 401);
        }
        // If user already exists (with or without a google_id) we do NOT override – ask user to login instead
        $existing = User::where('email', $googleEmail)->orWhere('google_id', $googleId)->first();
        if ($existing) {
            return $this->sendError(__('response.account_already_exists'), [], 409);
        }

        $data = $request->validated();
        $user = User::create([
            'email' => $googleEmail,
            'google_id' => $googleId,
            'name' => $data['name'],
            'phone_number' => $data['phone_number'] ?? null,
            'wilaya_id' => $data['wilaya_id'] ?? null,
            'commune_id' => $data['commune_id'] ?? null,
            'division_id' => $data['division_id'] ?? null,
            'age' => $data['age'] ?? null,
            'referral_source_id' => $data['referral_source_id'] ?? null,
            'email_verified_at' => Carbon::now(),
            'avatar_url' => $avatarUrl,
        ]);
        $role = Role::firstOrCreate(['name' => 'student']);
        $user->assignRole($role);

        $user->notify(new WelcomeNotification($user->name));

        $user->tokens()->delete();
        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.access_token_expiration')));
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::REFRESH_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.refresh_token_expiration')));

        return $this->sendResponse([
            'token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
        ], __('response.user_register_successfully'));
    }


    /**
     * Login with Google
     *
     * Authenticates a user via Google ID token, refreshes profile info, and issues new Sanctum tokens.
     */
    public function googleLogin(GoogleLoginRequest $request, GoogleAuthService $google)
    {
        $payload = $google->verifyIdToken($request->id_token);
        if (! $payload) {
            return $this->sendError(__('response.unauthorised'), ['error' => 'Invalid Google ID token'], 401);
        }
        $googleEmail = $payload['email'] ?? null;
        $googleId = $payload['sub'] ?? null;
        if (! $googleEmail || ! $googleId) {
            return $this->sendError(__('response.unauthorised'), ['error' => 'Google token missing email or sub'], 401);
        }

        $user = User::where('google_id', $googleId)->orWhere('email', $googleEmail)->first();
        if (! $user) {
            return $this->sendError(__('response.account_not_registered'), [], 401);
        }
        // Ensure google_id is set and refresh profile info from Google basic claims (name, picture) if present.
        $user->google_id = $googleId;
        if (isset($payload['name'])) {
            $user->name = $payload['name'];
        }
        if (isset($payload['picture']) && empty($user->avatar_url)) {
            $user->avatar_url = $payload['picture'];
        }
        $user->save();

        $user->tokens()->delete();
        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.access_token_expiration')));
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::REFRESH_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.refresh_token_expiration')));

        return $this->sendResponse([
            'token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
        ], __('response.user_login_successfully'));
    }

    /**
     * Register a new user.
     *
     * this endpoint takes the user name, email, phone number, and password and creates a new user, it also assigns the student role to the user, and returns the user data and a token
     */
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
            'referral_source_id' => $input['referral_source_id'] ?? null,
        ]);
        $role = Role::firstOrCreate(['name' => 'student']);
        $user->assignRole($role);

        // $token = $user->createToken($input["device_name"])->plainTextToken;
        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.access_token_expiration')));
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::REFRESH_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.refresh_token_expiration')));

        return $this->sendResponse([
            'token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            // 'user' => ResponseController::userRes($user),
        ], __('response.user_register_successfully'));
    }


    /**
     * Login a user.
     *
     * this endpoint takes the user email and password and logs the user in, it returns the user data and a token
     */
    public function login(LoginRequest $request)
    {
        $input = $request->all();

        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
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
                __('response.user_login_successfully')
            );
        } else {
            return $this->sendError(__('response.unauthorised'), ['error' => __('response.wrong_email_or_password')]);
        }
    }

    /**
     * Logout a user.
     *
     * This endpoint logs the user out and deletes the user token.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->sendResponse([], __('response.user_logout_successfully'));
    }

    /**
     * Refresh user token.
     *
     * This endpoint deletes the current user token and returns a new one.
     */
    public function refreshToken(Request $request)
    {
        // delete the old token (only the access_token)
        $request->user()->tokens()->where('name', 'access_token')->delete();
        $accessToken = $request->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.access_token_expiration')));

        return $this->sendResponse([
            'token' => $accessToken->plainTextToken,
        ], __('response.token_generated_successfully'));
    }

    /**
     * Check if email exists.
     *
     * This endpoint checks if an email exists in the system.
     */
    public function checkEmail(CheckEmailRequest $request)
    {
        $exists = User::where('email', $request->email)->exists();

        return $this->sendResponse([
            'message' => $exists ? __('response.email_exists') : __('response.email_not_exists'),
            'exists' => $exists,
        ]);
    }

    /**
     * Check if phone number exists.
     *
     * This endpoint checks if a phone number exists in the system.
     */
    public function checkPhoneNumber(CheckPhoneNumberRequest $request)
    {
        $exists = User::where('phone_number', $request->phone_number)->exists();

        return $this->sendResponse([
            'message' => $exists ? __('response.phone_exists') : __('response.phone_not_exists'),
            'exists' => $exists,
        ]);
    }
}
