<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\API\User\UpdateProfileRequest;
use Illuminate\Http\Request;
use Validator;

class UserController extends BaseController
{
    public function index(Request $request)
    {
        $user = $request->user();

        return $this->sendResponse([
            "user" => ResponseController::userRes($user),
        ]);
    }
    public function updateUser(UpdateProfileRequest $request)
    {
        $user = $request->user();
        if ($request->phone_number) {
            $user->phone_number = $request->phone_number;
        }
        if ($request->name) {
            $user->name = $request->name;
        }
        if ($request->wilaya_id) {
            $user->wilaya_id = $request->wilaya_id;
        }
        if ($request->commune_id) {
            $user->commune_id = $request->commune_id;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('avatars');
            $user->avatar_url = $path;
        }
        $user->save();

        return $this->sendResponse([
            "user" => ResponseController::userRes($user),
        ], "User updated successfully.");
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|min:3|max:32',
            'new_password' => 'required|confirmed|min:3|max:32',
        ]);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }
        $user = $request->user();
        if (!\Hash::check($request->current_password, $user->password)) {
            return $this->sendError("Current password is incorrect.");
        }
        $device_name = $user->currentAccessToken()->name;
        $user->currentAccessToken()->delete();
        $token = $user->createToken($device_name)->plainTextToken;

        $user->password = bcrypt($request->new_password);
        $user->save();

        return $this->sendResponse([
            "user" => ResponseController::userRes($user),
            "token" => $token,
        ], "Password updated successfully.");
    }
}
