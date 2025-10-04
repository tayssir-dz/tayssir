<?php

namespace App\Models;

use App\Enums\ContentDirection;
use App\Models\Pivot\ChapterUnit;
use App\Models\Pivot\MaterialUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Unit extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'material_id',
        'direction',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'direction' => ContentDirection::class,
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
    }
    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && $media->collection_name === 'image') {
            $this->addMediaConversion('thumb')
                ->width(width: 100)
                ->height(100);
        }
    }

    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('image') ? $this->getFirstMediaUrl('image') : null;
    }

    public function getImageThumbAttribute()
    {
        return $this->getFirstMediaUrl('image', 'thumb') ?: null;
    }


    public function material()
    {
        return $this->belongsToMany(Material::class)
            ->using(MaterialUnit::class)
            ->withPivot('sort')
            ->limit(1);
    }

    /**
     * Get the chapters for the unit.
     */
    public function chapters()
    {
        return $this->belongsToMany(Chapter::class)
            ->using(ChapterUnit::class)
            ->withPivot('sort')
            ->orderBy('chapter_unit.sort');
    }

    /**
     * Get the subscriptions for the unit.
     */
    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class);
    }

    /**
     * Get the effective direction based on inheritance rules.
     */
    public function getEffectiveDirection(): ContentDirection
    {
        if ($this->direction !== ContentDirection::INHERIT) {
            return $this->direction;
        }

        // Inherit from parent (Material)
        $material = $this->material()->first();

        return $material ? $material->direction : ContentDirection::RTL;
    }

    /**
     * Get the rtl attribute for backward compatibility.
     */
    public function getRtlAttribute()
    {
        return $this->getEffectiveDirection() === ContentDirection::RTL;
    }

    /**
     * Scope a query to only include active units.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    protected static function booted()
    {
        static::deleting(function ($unit) {
            foreach ($unit->chapters as $chapter) {
                $chapter->delete();
            }
        });
    }
}
