<?php

namespace App\Models;

use App\Traits\User\HasWilayaAndCommune;
use App\Traits\User\IsPanelUser;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Promoter extends Authenticatable implements FilamentUser, HasAvatar, HasMedia
{
    use HasFactory;
    use HasWilayaAndCommune;
    use InteractsWithMedia;
    use Notifiable;

    protected $fillable = ['name', 'phone_number', 'email', 'password', 'wilaya_id', 'commune_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getRecordTitleAttribute()
    {
        return $this->name . ' (' . $this->email . ')';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
    }
    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && $media->collection_name === 'avatar') {
            $this->addMediaConversion('thumb')
                ->width(100)
                ->height(100);
        }
    }

    public function getAvatarAttribute(): ?string
    {
        $media = $this->getFirstMedia('avatar');;
        return $media
            ? $this->getFirstMediaUrl('avatar', 'thumb')
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=111827';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('avatar');
    }

    public function promoCodes(): HasMany
    {
        return $this->hasMany(PromoCode::class);
    }

    public function manualPayments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,
            PromoCode::class,
            'promoter_id', // Foreign key on promo_codes referencing promoters
            'promo_code_id', // Foreign key on payments referencing promo_codes
            'id', // Local key on promoters
            'id' // Local key on promo_codes
        );
    }
}
