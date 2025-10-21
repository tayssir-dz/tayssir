<?php

namespace App\Filament\Admin\Widgets;

use App\Services\Analytics\EngagementAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EngagementMetrics extends BaseWidget
{
    protected string|array|int $columnSpan = 3;
    protected static ?int $sort = 4;

    public function getHeading(): ?string
    {
        return __('stats.engagement.title');
    }

    protected function getStats(): array
    {
        $analyticsService = new EngagementAnalyticsService();
        $stats = $analyticsService->getStats();

        return [
            // Lesson Completion Rate Card
            Stat::make(
                __('stats.engagement.lesson_completion_rate'),
                $stats['lesson_completion_rate']
            )
                ->description(__('stats.engagement.lesson_completion_rate_desc'))
                // ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            // Streak Count Card
            Stat::make(
                __('stats.engagement.streak_count'),
                $stats['streak_count']
            )
                ->description(__('stats.engagement.streak_count_desc'))
                // ->descriptionIcon('heroicon-o-fire')
                ->color('warning'),

            // Notification Open Rate Card
            Stat::make(
                __('stats.engagement.notification_open_rate'),
                $stats['notification_open_rate']
            )
                ->description(__('stats.engagement.notification_open_rate_desc'))
                // ->descriptionIcon('heroicon-o-bell')
                ->color('info'),
        ];
    }
}
