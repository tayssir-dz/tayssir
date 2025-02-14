<?php

namespace App\Models;

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
}
