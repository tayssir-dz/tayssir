<?php

namespace App\Services\Analytics;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TopWilayasChartService
{
    private const CACHE_DURATION = 3600; // 1 hour in seconds
    private const LIMIT = 10; // Top 10 wilayas

    /**
     * Get top wilayas chart data with filtering and caching
     *
     * @param string $filter Filter type: 'today', 'week', 'month', 'year', 'all'
     * @return array Chart data with labels and values
     */
    public function getChartData(string $filter = 'year'): array
    {
        $cacheKey = $this->getCacheKey($filter);

        // Get raw data with both names from cache
        $wilayasData = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($filter) {
            return $this->fetchWilayasData($filter);
        });

        // Format based on current locale
        return $this->formatChartData($wilayasData);
    }

    /**
     * Fetch wilayas data from database with both name fields
     */
    private function fetchWilayasData(string $filter): array
    {
        $now = now();

        // Build date constraint
        $dateConstraint = $this->getDateConstraint($filter, $now);

        // Get top wilayas with counts and both name fields
        $topWilayas = User::select('wilaya_id', DB::raw('COUNT(*) as count'))
            ->groupBy('wilaya_id')
            ->orderByDesc('count')
            ->limit(self::LIMIT)
            ->when($dateConstraint, function ($query) use ($dateConstraint) {
                $query->where('users.created_at', '>=', $dateConstraint);
            })
            ->with('wilaya:id,name,arabic_name')
            ->get();

        if ($topWilayas->isEmpty()) {
            return [];
        }

        // Store both names and count for locale-aware formatting
        return $topWilayas->map(function ($record) {
            return [
                'name' => $record->wilaya?->name ?? 'Unknown',
                'arabic_name' => $record->wilaya?->arabic_name ?? 'غير معروف',
                'count' => $record->count,
            ];
        })->toArray();
    }

    /**
     * Format chart data based on current locale
     */
    private function formatChartData(array $wilayasData): array
    {
        if (empty($wilayasData)) {
            return [
                'datasets' => [[
                    'label' => __('stats.suggestions.top_wilayas'),
                    'data' => [],
                ]],
                'labels' => [],
            ];
        }

        $locale = App::getLocale();
        $useArabic = $locale === 'ar';

        // Extract labels and data
        $labels = [];
        $data = [];
        foreach ($wilayasData as $wilaya) {
            $labels[] = $useArabic ? $wilaya['arabic_name'] : $wilaya['name'];
            $data[] = (int) $wilaya['count'];
        }

        // Color palette
        $baseColors = [
            '#00C4F6',
            '#12D18E',
            '#F85556',
            '#FF9500',
            '#F037A5',
            '#E5E7EB',
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
                'label' => __('stats.suggestions.top_wilayas'),
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
        return "analytics.top_wilayas.chart.{$filter}";
    }

    /**
     * Clear all top wilayas chart cache
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
