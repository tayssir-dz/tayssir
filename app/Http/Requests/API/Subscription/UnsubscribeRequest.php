<?php

namespace App\Http\Requests\API\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class UnsubscribeRequest extends FormRequest
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
            "subscription_id" => "required|numeric|exists:subscriptions,id",
            "password" => "required|string"
        ];
    }
}
