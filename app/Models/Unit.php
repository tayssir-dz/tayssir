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
     * Get the material that owns the unit.
     */

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the chapters for the unit.
     */

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
}
