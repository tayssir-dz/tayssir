<?php

namespace App\Http\Requests\API\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|nullable|string|min:4|max:255',
            'phone_number' => 'sometimes|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'profile_picture' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'wilaya_id' => 'sometimes|nullable|numeric|exists:wilayas,id',
            'commune_id' => 'sometimes|nullable|numeric|exists:communes,id|exists:communes,id,wilaya_id,' . $this->wilaya_id,
            'age' => 'sometimes|nullable|numeric|min:1|max:120',
            'division_id' => 'sometimes|nullable|numeric|exists:divisions,id',
        ];
    }
}
