<?php

namespace App\Services\Analytics;

use App\Models\User;
use App\Models\UserAnswer;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SuggestionsAnalyticsService
{
    private const CACHE_DURATION = 3600; // 1 hour in seconds
    private const CACHE_KEY = 'analytics.suggestions_stats';

    /**
     * Get all suggestions metrics with a single cache call
     */
    public function getStats(): array
    {
        $stats = Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return [
                'top_wilayas' => $this->calculateTopWilayas(),
                'peak_hours' => $this->calculatePeakHours(),
            ];
        });

        // Format top wilayas based on current locale
        $stats['top_wilayas'] = $this->formatWilayasForLocale($stats['top_wilayas']);

        return $stats;
    }

    /**
     * Calculate top wilayas by user count - stores both names
     */
    private function calculateTopWilayas(): array
    {
        $topWilayas = User::select('wilaya_id', DB::raw('COUNT(*) as count'))
            ->groupBy('wilaya_id')
            ->orderByDesc('count')
            ->limit(3)
            ->with('wilaya:id,name,arabic_name')
            ->get();

        if ($topWilayas->isEmpty()) {
            return [];
        }

        // Cache the raw data with both names
        return $topWilayas->map(function ($record) {
            return [
                'name' => $record->wilaya?->name ?? 'Unknown',
                'arabic_name' => $record->wilaya?->arabic_name ?? 'غير معروف',
                'count' => $record->count,
            ];
        })->toArray();
    }

    /**
     * Format wilayas display based on current locale
     */
    private function formatWilayasForLocale(array $wilayas): string
    {
        if (empty($wilayas)) {
            return 'No data';
        }

        $locale = App::getLocale();
        $useArabic = $locale === 'ar';

        $formatted = collect($wilayas)->map(function ($wilaya) use ($useArabic) {
            $name = $useArabic ? $wilaya['arabic_name'] : $wilaya['name'];
            return "{$name}: {$wilaya['count']}";
        })->join(' ');

        return $formatted;
    }

    /**
     * Calculate peak usage hours (simplified - returns static placeholder)
     * In a real scenario, this would analyze user_answers timestamps
     */
    private function calculatePeakHours(): string
    {
        // Placeholder: Returns Sunday - Wednesday, 19:00 - 21:00
        // This is based on typical learning platform usage patterns
        return 'الأحد - الأربعاء، 19:00 - 21:00';
    }

    /**
     * Clear suggestions analytics cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
