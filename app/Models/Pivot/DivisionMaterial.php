<?php

namespace App\Models\Pivot;

use App\Models\Division;
use App\Models\Material;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DivisionMaterial extends Pivot
{
    protected $table = 'division_material';

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
