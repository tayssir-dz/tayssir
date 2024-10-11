<?php

namespace App\Filament\Resources\CardResource;

// export a class that has a static function 'testo'

class CardFormUtils
{
    public static function priceStateHandeler($get, $set)
    {
        $price = preg_replace('/[^0-9]/', '', $get('price'));
        $discountPrice = $get('discount_price');
        $discountPercentage = $get('discount_percentage');
        if ($discountPercentage == null) {
            $set('discount_percentage', 0);
            $discountPercentage = 0;
        }
        $isOnDiscount = $get('is_on_discount');
        if ($isOnDiscount) {
            $price = ($price - ($price * ($discountPercentage / 100))) - $discountPrice;
            $set('display_price', $price);
        } else {
            $set('display_price', $price);
        }
    }
}
