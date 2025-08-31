<?php

namespace App\Models;

use App\Enums\Purchase\PaymentRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ManualPayment extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'promo_code_id',
        'status',
        'price',
        'discount_percentage',
        'discount_amount',
        'promocode_percentage',
        'promocode_amount',
        'combined_discount_percentage',
        'combined_discount_amount',
        'final_price',
        'promoter_margin_percentage',
        'promoter_margin_amount',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentRequestStatus::class,
            'price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'promocode_percentage' => 'decimal:2',
            'promocode_amount' => 'decimal:2',
            'combined_discount_percentage' => 'decimal:2',
            'combined_discount_amount' => 'decimal:2',
            'final_price' => 'decimal:2',
            'promoter_margin_percentage' => 'decimal:2',
            'promoter_margin_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachment')
            ->useDisk('private')
            ->singleFile();
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('attachment');
    }
}
