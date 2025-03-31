<?php

namespace App\Models\Pivot;

use App\Models\Material;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MaterialUnit extends Pivot
{
    protected $table = 'material_unit';

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
