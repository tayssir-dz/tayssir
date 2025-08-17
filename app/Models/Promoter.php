<?php

namespace App\Models;

use App\Traits\User\HasWilayaAndCommune;
use App\Traits\User\IsPanelUser;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Promoter extends Authenticatable implements FilamentUser, HasAvatar, HasMedia
{
    use HasFactory;
    use HasWilayaAndCommune;
    use InteractsWithMedia;
    use IsPanelUser;
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
        return $this->name.' ('.$this->email.')';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
    }

    public function getAvatarAttribute(): ?string
    {
        $media = $this->getFirstMedia('avatar');

        return $media ? $media->getUrl() : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=FFFFFF&background=111827';
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('avatar');
    }

    public function promoCodes(): HasMany
    {
        return $this->hasMany(PromoCode::class);
    }
}
