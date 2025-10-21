<?php

namespace App\Filament\Admin\Widgets;

use App\Services\Analytics\ContentQualityService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentQualityMetrics extends BaseWidget
{
    protected string|array|int $columnSpan = 3;
    protected static ?int $sort = 5;

    public function getHeading(): ?string
    {
        return __('stats.content_quality.title');
    }

    protected function getStats(): array
    {
        $service = new ContentQualityService();
        $metrics = $service->getMetrics();

        // Format top materials for display
        $topMaterialsDisplay = collect($metrics['top_materials'])
            ->map(fn($item) => $item['material_name'])
            ->join(', ');

        // Format bottom materials for display
        $bottomMaterialsDisplay = collect($metrics['bottom_materials'])
            ->map(fn($item) => $item['material_name'])
            ->join(', ');

        return [
            // Most Engaged Materials
            Stat::make(
                __('stats.content_quality.top_materials_title'),
                $topMaterialsDisplay ?: __('stats.content_quality.no_data')
            )
                ->description(__('stats.content_quality.top_materials_desc'))
                ->color('success'),

            // Least Engaged Materials
            Stat::make(
                __('stats.content_quality.bottom_materials_title'),
                $bottomMaterialsDisplay ?: __('stats.content_quality.no_data')
            )
                ->description(__('stats.content_quality.bottom_materials_desc'))
                ->color('warning'),
        ];
    }
}
