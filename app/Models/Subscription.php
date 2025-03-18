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
}
