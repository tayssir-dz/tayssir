<?php

namespace App\Filament\Widgets;

use Spatie\FilamentSimpleStats\SimpleStat;

class MoneyStat extends SimpleStat
{
    protected function formatFaceValue(int|float $total): string
    {
        if ($total > 1000) {
            return number_format($total / 1000, 2).'r';
        }

        return $total;
    }
}
