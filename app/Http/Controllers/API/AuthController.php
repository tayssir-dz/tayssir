<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\ResponseController;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Validator;
use Auth;

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
        ]);
        $role = Role::firstOrCreate(['name' => 'student']);
        $user->assignRole($role);


        $token = $user->createToken($input["device_name"])->plainTextToken;

        return $this->sendResponse([
            'user' => ResponseController::userRes($user),
            'token' => $token,
        ], 'User register successfully.');
    }
    public function login(LoginRequest $request)
    {
        $input = $request->all();

        if (Auth::attempt(['email' => $input["email"], 'password' => $input["password"]])) {
            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken($input["device_name"])->plainTextToken;

            return $this->sendResponse([
                'user' => ResponseController::userRes($user),
                'token' => $token,
            ], 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Wrong email or password.']);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 'User logout successfully.');
    }

    public function refreshToken(Request $request)
    {
        $user = $request->user();
        $device_name = $user->currentAccessToken()->name;
        $user->currentAccessToken()->delete();
        $token = $user->createToken($device_name)->plainTextToken;

        return $this->sendResponse([
            "user" => ResponseController::userRes($user),
            "token" => $token
        ], 'Token refreshed successfully');
    }
}
