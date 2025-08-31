<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'promoter_id',
        'code',
        'start_date',
        'end_date',
        'student_discount',
        'promoter_margin',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'student_discount' => 'decimal:2',
            'promoter_margin' => 'decimal:2',
        ];
    }

    public function promoter(): BelongsTo
    {
        return $this->belongsTo(Promoter::class);
    }

    public function getIsActiveAttribute(): bool
    {
        $now = Carbon::now()->toDateString();

        return $this->start_date <= $now && $this->end_date >= $now;
    }

    public function manualPayments()
    {
        return $this->hasMany(Payment::class);
    }
}
