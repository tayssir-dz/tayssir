<?php

namespace App\Services\Analytics;

use App\Models\ReferralSource;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReferralSourcesChartService
{
    private const CACHE_DURATION = 3600; // 1 hour in seconds

    /**
     * Get referral sources chart data with filtering and caching
     *
     * @param string $filter Filter type: 'today', 'week', 'month', 'year', 'all'
     * @return array Chart data with labels and values
     */
    public function getChartData(string $filter = 'year'): array
    {
        $cacheKey = $this->getCacheKey($filter);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($filter) {
            return $this->fetchChartData($filter);
        });
    }

    /**
     * Fetch chart data from database
     */
    private function fetchChartData(string $filter): array
    {
        $now = now();

        // Get all referral sources for consistent labeling
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

        // Build date constraint
        $dateConstraint = $this->getDateConstraint($filter, $now);

        // Aggregate counts in one query
        $counts = User::query()
            ->select('referral_source_id', DB::raw('count(*) as aggregate'))
            ->whereIn('referral_source_id', $sources->pluck('id')->all())
            ->when($dateConstraint, function ($query) use ($dateConstraint) {
                $query->where('users.created_at', '>=', $dateConstraint);
            })
            ->groupBy('referral_source_id')
            ->pluck('aggregate', 'referral_source_id');

        // Build labels and data arrays
        $labels = [];
        $data = [];
        foreach ($sources as $source) {
            $labels[] = $source->name;
            $data[] = (int) ($counts[$source->id] ?? 0);
        }

        // Simple color palette
        $baseColors = [
            '#00C4F6',
            '#12D18E',
            '#F85556',
            '#FF9500',
            '#F037A5',
            '#E5E7EB',
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

    /**
     * Get date constraint based on filter type
     */
    private function getDateConstraint(string $filter, $now)
    {
        return match ($filter) {
            'today' => $now->copy()->startOfDay(),
            'week' => $now->copy()->startOfWeek(),
            'month' => $now->copy()->startOfMonth(),
            'year' => $now->copy()->startOfYear(),
            default => null, // 'all' or any other value
        };
    }

    /**
     * Generate cache key based on filter
     */
    private function getCacheKey(string $filter): string
    {
        return "analytics.referral_sources.chart.{$filter}";
    }

    /**
     * Clear all referral sources chart cache
     */
    public function clearCache(): void
    {
        $filters = ['today', 'week', 'month', 'year', 'all'];
        foreach ($filters as $filter) {
            Cache::forget($this->getCacheKey($filter));
        }
    }

    /**
     * Clear cache for specific filter
     */
    public function clearCacheForFilter(string $filter): void
    {
        Cache::forget($this->getCacheKey($filter));
    }
}
