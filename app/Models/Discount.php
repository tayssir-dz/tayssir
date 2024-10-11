<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
        "amount",
        "percentage",
        "from",
        "to",
    ];

    protected function casts(): array
    {
        return [
            'from' => 'date',
            'to' => 'date',
        ];
    }

    public function subscriptions()
    {
        return $this->belongsToMany(related: Subscription::class);
    }
}
