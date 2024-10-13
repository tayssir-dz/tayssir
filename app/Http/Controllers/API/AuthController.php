<?php

namespace App\Http\Controllers\API;

use App\Enums\TokenAbility;
use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\ResponseController;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Validator;
use Auth;
use G4T\Swagger\Attributes\SwaggerSection;

#[SwaggerSection('This section manages all authentication-related actions such as user registration, login, logout, and token refresh. It ensures secure authentication processes, handling both token-based and user-based operations for registering and logging users in and out of the system.')]
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
            'wilaya_id' => $input['wilaya_id'],
            'commune_id' => $input['commune_id'],
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
        ], 'User register successfully.');
    }
    public function login(LoginRequest $request)
    {
        $input = $request->all();

        if (Auth::attempt(['email' => $input["email"], 'password' => $input["password"]])) {
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
                'User login successfully.'
            );

        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Wrong email or password.']);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->sendResponse([], 'User logout successfully.');
    }

    public function refreshToken(Request $request)
    {
        // delete the old token (only the access_token)
        $request->user()->tokens()->where('name', 'access_token')->delete();
        $accessToken = $request->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.access_token_expiration')));

        return $this->sendResponse([
            "token" => $accessToken->plainTextToken,
        ], 'Token generated successfully');
    }
}
