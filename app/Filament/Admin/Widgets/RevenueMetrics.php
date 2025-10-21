<?php

namespace App\Filament\Admin\Widgets;

use App\Services\Analytics\RevenueAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueMetrics extends BaseWidget
{
    protected string|array|int $columnSpan = 3;
    protected static ?int $sort = 5;

    public function getHeading(): ?string
    {
        return __('stats.revenue.title');
    }

    protected function getStats(): array
    {
        $analyticsService = new RevenueAnalyticsService();
        $stats = $analyticsService->getStats();

        return [
            // Active Subscriptions Card
            Stat::make(
                __('stats.revenue.active_subscriptions'),
                $stats['active_subscriptions']
            )
                ->description(__('stats.revenue.active_subscriptions_desc'))
                // ->descriptionIcon('heroicon-o-credit-card')
                ->color('success'),
        ];
    }
}
