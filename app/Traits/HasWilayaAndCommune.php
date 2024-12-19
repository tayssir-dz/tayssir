<?php

namespace App\Traits;

use Kossa\AlgerianCities\Commune;
use Kossa\AlgerianCities\Wilaya;

trait HasWilayaAndCommune
{
    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
}
