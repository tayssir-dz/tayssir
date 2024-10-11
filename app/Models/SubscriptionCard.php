<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionCard extends Model
{
    use HasFactory;

    protected $fillable = [
        "code",
        "user_id",
        "subscription_id",
        "redeemed_at",
    ];

    protected $with = ["subscription"];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
