<?php

namespace App\Filament\Admin\Widgets;

use App\Services\Analytics\SuggestionsAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuggestionsMetrics extends BaseWidget
{
    protected string|array|int $columnSpan = 3;
    protected static ?int $sort = 6;

    public function getHeading(): ?string
    {
        return __('stats.suggestions.title');
    }

    protected function getStats(): array
    {
        $analyticsService = new SuggestionsAnalyticsService();
        $stats = $analyticsService->getStats();

        return [
            // Top Wilayas Card
            Stat::make(
                __('stats.suggestions.top_wilayas'),
                $stats['top_wilayas']
            )
                ->description(__('stats.suggestions.top_wilayas_desc'))
                // ->descriptionIcon('heroicon-o-map-pin')
                ->color('info'),

            // Peak Hours Card
            // Stat::make(
            //     __('stats.suggestions.peak_hours'),
            //     $stats['peak_hours']
            // )
            //     ->description(__('stats.suggestions.peak_hours_desc'))
            //     // ->descriptionIcon('heroicon-o-clock')
            //     ->color('warning'),
        ];
    }
}
