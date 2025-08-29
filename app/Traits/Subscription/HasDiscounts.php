<?php

namespace App\Traits\Subscription;

trait HasDiscounts
{
    /**
     * Get the price after applying the best available discount
     */
    public function getPriceAfterDiscountAttribute()
    {
        $originalPrice = $this->price;

        // Get active discounts (within date range)
        $activeDiscounts = $this->discounts()
            ->where(function ($query) {
                $query->where('from', '<=', now())
                    ->where('to', '>=', now());
            })
            ->get();

        if ($activeDiscounts->isEmpty()) {
            return $originalPrice;
        }

        $finalPrice = $originalPrice;
        $totalPercentageDiscount = 0;

        // Collect all discounts
        foreach ($activeDiscounts as $discount) {
            // Add up all percentage discounts
            if ($discount->percentage && $discount->percentage > 0) {
                $totalPercentageDiscount += $discount->percentage;
            }
        }

        // Apply percentage discounts
        if ($totalPercentageDiscount > 0) {
            $finalPrice = $originalPrice * (1 - ($totalPercentageDiscount / 100));
        }

        return $finalPrice;
    }

    /**
     * Get the discount amount applied
     */
    public function getDiscountAmountAttribute()
    {
        return $this->price - $this->price_after_discount;
    }

    /**
     * Get the discount percentage applied
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->price == 0) {
            return 0;
        }

        return (($this->price - $this->price_after_discount) / $this->price) * 100;
    }
}
