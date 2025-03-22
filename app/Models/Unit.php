<?php

namespace App\Models;

use App\Enums\ContentDirection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

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
     * Get the materials for the unit.
     */
    public function material_units()
    {
        return $this->belongsToMany(Material::class)
            ->withPivot('sort')
            ->orderBy('material_unit.sort');
    }

    public function getMaterialAttribute()
    {
        return $this->material_units()->first();
    }

    /**
     * Get the chapters for the unit.
     */

    public function chapters()
    {
        return $this->belongsToMany(Chapter::class)
            ->withPivot('sort')
            ->orderBy('chapter_unit.sort')
            ->orderBy('chapters.id');
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
        $material = $this->material;
        return $material ? $material->direction : ContentDirection::RTL;
    }

    /**
     * Get the rtl attribute for backward compatibility.
     */
    public function getRtlAttribute()
    {
        return $this->getEffectiveDirection() === ContentDirection::RTL;
    }
}
