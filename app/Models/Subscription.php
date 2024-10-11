<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
        "price",
        "ending_date",
    ];

    // protected $with = ["subscriptionCards"];

    protected function casts(): array
    {
        return [
            'ending_date' => 'date',
        ];
    }

    public function subscriptionCards(): HasMany
    {
        return $this->hasMany(SubscriptionCard::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(related: Discount::class);
    }
}
