<?php

namespace App\Filament\Promoter\Widgets;

class PromoterWidgets
{
    /**
     * @return array<class-string>
     */
    public static function get(): array
    {
        return [
            PromoterStats::class,
            MostUsedPromoCode::class,
        ];
    }
}
