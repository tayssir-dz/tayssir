<?php

namespace App\Models;

use App\Enums\ContentDirection;
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
    }

    /**
     * Get the image attribute.
     */
    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('image') ? $this->getFirstMediaUrl('image') : null;
    }

    /**
     * Get the divisions for the material.
     */
    public function division_materials()
    {
        return $this->belongsToMany(Division::class)
            ->withPivot('sort')
            ->orderBy('division_material.sort');
    }

    /**
     * Get the divisions for the material.
     * Alias for division_materials() for better naming consistency.
     */
    public function divisions()
    {
        return $this->division_materials();
    }

    public function getDivisionAttribute()
    {
        return $this->division_materials()->first();
    }

    /**
     * Get the units for the material.
     */

    public function units()
    {
        return $this->belongsToMany(Unit::class)
            ->withPivot('sort')
            ->orderBy('material_unit.sort')
        ;
    }

    /**
     * Get the rtl attribute for backward compatibility.
     */
    public function getRtlAttribute()
    {
        return $this->direction === ContentDirection::RTL;
    }
}
