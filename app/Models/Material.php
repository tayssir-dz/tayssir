<?php

namespace App\Models;

use App\Enums\ContentDirection;
use App\Models\Pivot\DivisionMaterial;
use App\Models\Pivot\MaterialUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Material extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'code',
        'color',
        'secondary_color',
        'description',
        'division_id',
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

    /**
     * Register the media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();

        $this->addMediaCollection('image_grid')
            ->singleFile();
    }

    /**
     * Get the image attribute.
     */
    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('image') ? $this->getFirstMediaUrl('image') : null;
    }

    /**
     * Get the image_grid attribute.
     */
    public function getImageGridAttribute()
    {
        return $this->getFirstMediaUrl('image_grid') ? $this->getFirstMediaUrl('image_grid') : null;
    }

    /**
     * Get the divisions for the material.
     */
    public function divisions()
    {
        return $this->belongsToMany(Division::class)
            ->using(DivisionMaterial::class)
            ->withPivot('sort');
    }



    public function units()
    {
        return $this->belongsToMany(Unit::class)
            ->using(MaterialUnit::class)
            ->withPivot('sort')
            ->orderBy('material_unit.sort');
    }

    /**
     * Get the rtl attribute for backward compatibility.
     */
    public function getRtlAttribute()
    {
        return $this->direction === ContentDirection::RTL;
    }

    /**
     * Scope a query to only include active materials.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
