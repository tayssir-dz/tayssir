<?php

namespace App\Filament\Admin\Widgets;

use App\Services\Analytics\TopWilayasChartService;
use Filament\Widgets\ChartWidget;

class TopWilayasBarChart extends ChartWidget
{
    protected static ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = 4;
    protected static ?int $sort = 2;
    public ?string $filter = 'year';

    protected function getType(): string
    {
        return 'bar';
    }

    public function getHeading(): string
    {
        return __('stats.suggestions.top_wilayas');
    }

    protected function getFilters(): array
    {
        return [
            'today' => __('custom.stats.referral_sources.filter.today'),
            'week' => __('custom.stats.referral_sources.filter.week'),
            'month' => __('custom.stats.referral_sources.filter.month'),
            'year' => __('custom.stats.referral_sources.filter.year'),
            'all' => __('custom.stats.referral_sources.filter.all'),
        ];
    }

    protected function getData(): array
    {
        $service = new TopWilayasChartService();
        return $service->getChartData($this->filter);
    }
}
