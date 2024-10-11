<?php

namespace App\Observers;

use App\Models\Card;

class CardObserver
{
    /**
     * Handle the Card "created" event.
     */
    public function created(Card $card): void
    {
        if ($card->is_on_discount) {
            $price = $card->price;
            $discountPrice = $card->discount_price;
            $discountPercentage = $card->discount_percentage;
            $card->display_price = ($price - ($price * ($discountPercentage / 100))) - $discountPrice;
            Card::where('id', $card->id)->update([
                'display_price' => $card->display_price
            ]);
        } else {
            $card->display_price = $card->price;
            Card::where('id', $card->id)->update([
                'display_price' => $card->price
            ]);
        }
    }

    /**
     * Handle the Card "updated" event.
     */
    public function updated(Card $card): void
    {
        // dirty check
        if ($card->isDirty('is_on_discount') || $card->isDirty('price') || $card->isDirty('discount_price') || $card->isDirty('discount_percentage')) {
            if ($card->is_on_discount) {
                $price = $card->price;
                $discountPrice = $card->discount_price;
                $discountPercentage = $card->discount_percentage;
                $card->display_price = ($price - ($price * ($discountPercentage / 100))) - $discountPrice;
                Card::where('id', $card->id)->update([
                    'display_price' => $card->display_price
                ]);
            } else {
                $card->display_price = $card->price;
                Card::where('id', $card->id)->update([
                    'display_price' => $card->price
                ]);
            }
        }

    }

    /**
     * Handle the Card "deleted" event.
     */
    public function deleted(Card $card): void
    {
        //
    }

    /**
     * Handle the Card "restored" event.
     */
    public function restored(Card $card): void
    {
        //
    }

    /**
     * Handle the Card "force deleted" event.
     */
    public function forceDeleted(Card $card): void
    {
        //
    }
}
