<?php

namespace App\Filament\Widgets;

use App\Models\ReferralSource;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReferralSourcesBarChart extends ChartWidget
{
    protected static ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = 3;
    protected static ?int $sort = 1;
    public ?string $filter = 'year';

    protected function getType(): string
    {
        return 'bar';
    }

    public function getHeading(): string
    {
        return __('custom.stats.referral_sources.widget.title');
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
        $now = now();

        $dateConstraint = function ($query) use ($now) {
            switch ($this->filter) {
                case 'today':
                    $query->where('users.created_at', '>=', $now->copy()->startOfDay());
                    break;
                case 'week':
                    $query->where('users.created_at', '>=', $now->copy()->startOfWeek());
                    break;
                case 'month':
                    $query->where('users.created_at', '>=', $now->copy()->startOfMonth());
                    break;
                case 'year':
                    $query->where('users.created_at', '>=', $now->copy()->startOfYear());
                    break;
                case 'all':
                default:
                    // no constraint
                    break;
            }
        };

        // Get all referral sources for consistent labeling even if count is zero
        $sources = ReferralSource::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($sources->isEmpty()) {
            return [
                'datasets' => [[
                    'label' => __('custom.stats.referral_sources.dataset.users'),
                    'data' => [],
                ]],
                'labels' => [],
            ];
        }

        // Aggregate counts in one query
        $counts = User::query()
            ->select('referral_source_id', DB::raw('count(*) as aggregate'))
            ->whereIn('referral_source_id', $sources->pluck('id')->all())
            ->when($this->filter !== 'all', $dateConstraint)
            ->groupBy('referral_source_id')
            ->pluck('aggregate', 'referral_source_id');

        $labels = [];
        $data = [];
        foreach ($sources as $source) {
            $labels[] = $source->name;
            $data[] = (int) ($counts[$source->id] ?? 0);
        }

        // Simple color palette (repeat if needed)
        $baseColors = [
            '#3b82f6',
            '#10b981',
            '#f59e0b',
            '#ef4444',
            '#6366f1',
            '#8b5cf6',
            '#ec4899',
            '#14b8a6',
            '#0ea5e9',
            '#84cc16',
        ];
        $colors = [];
        $countLabels = count($labels);
        for ($i = 0; $i < $countLabels; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }

        return [
            'datasets' => [[
                'label' => __('custom.stats.referral_sources.dataset.users'),
                'data' => $data,
                'backgroundColor' => $colors,
                'borderRadius' => 4,
            ]],
            'labels' => $labels,
        ];
    }
}
