<?php

namespace App\Http\Requests\API\V2;

use Illuminate\Foundation\Http\FormRequest;

class CheckPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust authorization if needed
    }

    public function rules(): array
    {
        return [
            'subscription_id' => ['required', 'integer', 'exists:subscriptions,id'],
            'promocode' => ['nullable', 'string', 'exists:promo_codes,code'],
        ];
    }
}
