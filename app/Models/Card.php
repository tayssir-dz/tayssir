<?php

namespace App\Models;

use App\Observers\CardObserver;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([CardObserver::class])]
class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        "code",
        "price",
        "is_on_discount",
        "discount_price",
        "discount_percentage",
        'display_price',
        'status',
        "subscription_type",
        "user_id",
        "activated_at",
        "expires_at",
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function getPriceAttribute($value)
    // {
    //     return $value / 100;
    // }

    // public function getDisplayPriceAttribute($value)
    // {
    //     return $value / 100;
    // }
}
