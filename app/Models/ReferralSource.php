<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReferralSource extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && $media->collection_name === 'icon') {
            $this->addMediaConversion('thumb')
                ->width(width: 150)
                ->height(150);
        }
    }

    /**
     * Accessor for icon url (svg).
     */
    public function getIconAttribute()
    {
        return $this->getFirstMediaUrl('icon', 'thumb') ?: null;
    }

    /**
     * Users that belong to this referral source.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Accessor to get number of users for this referral source.
     */
    public function getUsersCountAttribute(): int
    {
        // Use loaded relation if present to avoid extra query
        if ($this->relationLoaded('users')) {
            return $this->users->count();
        }

        return $this->users()->count();
    }
}
