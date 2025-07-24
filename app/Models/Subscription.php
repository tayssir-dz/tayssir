<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    public const GUEST_ID = 1;

    protected $fillable = [
        "name",
        "description",
        "price",
        "ending_date",
        "gradiant_start",
        "gradiant_end",
        "bottom_color_at_start",
    ];

    // protected $with = ["subscriptionCards"];

    protected function casts(): array
    {
        return [
            'ending_date' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($subscription) {
            // Prevent deletion of guest subscription
            if ($subscription->id === self::GUEST_ID) {
                return false;
            }
        });
    }

    public static function guest()
    {
        return static::find(self::GUEST_ID);
    }

    public function subscriptionCards(): HasMany
    {
        return $this->hasMany(SubscriptionCard::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(related: Discount::class);
    }

    public function chapters()
    {
        return $this->belongsToMany(Chapter::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class);
    }

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

        $bestPrice = $originalPrice;

        foreach ($activeDiscounts as $discount) {
            $discountedPrice = $originalPrice;

            // Apply percentage discount
            if ($discount->percentage) {
                $discountedPrice = $originalPrice * (1 - ($discount->percentage / 100));
            }

            // Apply fixed amount discount
            if ($discount->amount) {
                $discountedPrice = max(0, $originalPrice - $discount->amount);
            }

            // Keep the best (lowest) price
            $bestPrice = min($bestPrice, $discountedPrice);
        }

        return $bestPrice;
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
