<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\API\ResponseController;
use App\Http\Requests\API\User\UpdateProfileRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// #[SwaggerSection('This section handles all user-related operations such as retrieving user information, updating user details like name, email, and phone number, and allowing users to change their password securely. It enforces proper user authentication and authorization to protect user data.')]
#[Group('User Management APIs', weight: 3)]
class UserController extends BaseController
{
    /**
     * Get user infos.
     *
     * This endpoint returns the user data.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return $this->sendResponse([
            'user' => ResponseController::userRes($user),
        ]);
    }

    /**
     * Update user infos.
     *
     * This endpoint takes the user name, email, and phone number and updates the user data.
     */
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
        if ($request->age) {
            $user->age = $request->age;
        }
        if ($request->division_id) {
            $user->division_id = $request->division_id;
        }
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $path = $image->store('avatars', 'public');
            $user->avatar_url = $path;
        }
        $user->save();

        return $this->sendResponse([
            'user' => ResponseController::userRes($user),
        ], __('response.user_updated_successfully'));
    }

    /**
     * Change user password.
     *
     * This endpoint takes the old password and the new password and changes the user password.
     */
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
        if (! Hash::check($request->current_password, $user->password)) {
            return $this->sendError(__('response.current_password_is_incorrect'));
        }
        $device_name = $user->currentAccessToken()->name;
        $user->currentAccessToken()->delete();
        $token = $user->createToken($device_name)->plainTextToken;

        $user->password = bcrypt($request->new_password);
        $user->save();

        return $this->sendResponse([
            'user' => ResponseController::userRes($user),
            'token' => $token,
        ], __('response.password_updated_successfully'));
    }
}
