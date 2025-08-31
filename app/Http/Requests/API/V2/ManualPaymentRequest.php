<?php

namespace App\Http\Requests\API\V2;

use App\Models\PromoCode;
use App\Models\ManualPayment;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManualPaymentRequest extends FormRequest
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
            'subscription_id' => [
                'required',
                'integer',
                'exists:subscriptions,id',
                function ($attribute, $value, $fail) {
                    $user = $this->user();
                    if (! $user) {
                        return; // Auth middleware should enforce, but guard anyway
                    }
                    $hasPending = Payment::where('user_id', $user->id)
                        ->where('status', 'pending')
                        ->exists();
                    if ($hasPending) {
                        $fail('You already have a pending manual payment request. Please wait until it is reviewed.');
                    }
                },
            ],
            'promocode' => [
                'nullable',
                'string',
                Rule::exists('promo_codes', 'code'), // Use Rule for better readability
                function ($attribute, $value, $fail) {
                    if (! $value) {
                        return;
                    }
                    $promo = PromoCode::where('code', $value)->first();
                    if ($promo && ! $promo->is_active) {
                        $fail('The provided promo code is not active.');
                    }
                },
            ],
            'attachment' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'], // Max 5MB file
        ];
    }
}
