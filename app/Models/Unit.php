<?php

namespace App\Models;

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
}
