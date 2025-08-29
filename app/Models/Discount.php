<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'percentage',
        'from',
        'to',
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

    public function getIsActiveAttribute(): bool
    {
        $now = Carbon::now()->toDateString();

        return $this->from <= $now && $this->to >= $now;
    }
}
