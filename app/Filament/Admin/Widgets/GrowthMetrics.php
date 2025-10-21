<?php

namespace App\Filament\Admin\Widgets;

use App\Services\Analytics\GrowthAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GrowthMetrics extends BaseWidget
{
    protected string|array|int $columnSpan = 4;
    protected static ?int $sort = 3;

    public function getHeading(): ?string
    {
        return __('stats.growth.title');
    }

    protected function getStats(): array
    {
        $analyticsService = new GrowthAnalyticsService();
        $stats = $analyticsService->getStats();

        // Format signups by source for display
        $signupsDisplay = collect($stats['new_signups_by_source'])
            ->map(fn($item) => "{$item['source']}: {$item['count']}")
            ->join(', ');

        return [
            // New Signups by Source Card
            Stat::make(
                __('stats.growth.new_signups_by_source'),
                $signupsDisplay ?: 'No data'
            )
                ->description(__('stats.growth.new_signups_by_source_desc'))
                ->color('primary'),

            // Activation Rate Card
            Stat::make(
                __('stats.growth.activation_rate'),
                $stats['activation_rate']
            )
                ->description(__('stats.growth.activation_rate_desc'))
                ->color('success'),

            // Conversion Rate Card
            Stat::make(
                __('stats.growth.conversion_rate'),
                $stats['conversion_rate']
            )
                ->description(__('stats.growth.conversion_rate_desc'))
                ->color('warning'),

            // Top Referrer Card
            Stat::make(
                __('stats.growth.top_referrer'),
                $stats['top_referrer']['code'] . ' = ' . $stats['top_referrer']['count'] . ' ' . __('stats.growth.students_label')
            )
                ->description(__('stats.growth.top_referrer_desc'))
                ->color('info'),
        ];
    }
}
