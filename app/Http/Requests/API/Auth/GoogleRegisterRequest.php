<?php

namespace App\Http\Requests\API\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GoogleRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_token' => 'required|string',
            'name' => 'required|min:4|max:255',
            'age' => 'required|numeric|min:12',
            'phone_number' => 'sometimes|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users,phone_number',
            'wilaya_id' => 'sometimes|nullable|numeric|exists:wilayas,id',
            'commune_id' => 'sometimes|nullable|numeric|exists:communes,id|exists:communes,id,wilaya_id,' . $this->wilaya_id,
            'division_id' => 'required|numeric|exists:divisions,id',
            'referral_source_id' => 'sometimes|nullable|numeric|exists:referral_sources,id',
        ];
    }
}
