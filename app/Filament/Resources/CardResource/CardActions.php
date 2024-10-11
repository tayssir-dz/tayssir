<?php

namespace App\Filament\Resources\CardResource;
use App\Models\Card;

// export a class that has a static function 'testo'

class CardActions
{
    public static function createManyCards(array $data): void
    {
        for ($i = 0; $i < $data["number_of_cards"]; $i++) {
            $code = rand(100000000000, 999999999999);
            $discount_price = 0;
            $discount_percentage = 0;
            if ($data['is_on_discount']) {
                $discount_price = $data['discount_price'];
                $discount_percentage = $data['discount_percentage'];
            }
            try {
                Card::create([
                    'code' => $code,
                    'price' => $data['price'],
                    'is_on_discount' => $data['is_on_discount'],
                    'discount_price' => $discount_price,
                    'discount_percentage' => $discount_percentage,
                    'subscription_type' => $data['subscription_type'] ? $data['subscription_type'] : "yearly_subscription",
                    'expires_at' => $data['expires_at'],
                ]);
            } catch (\Exception $e) {
            }
        }
    }
}
