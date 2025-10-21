<?php

namespace App\Services\Analytics;

use App\Models\SubscriptionCard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RevenueAnalyticsService
{
    private const CACHE_DURATION = 3600; // 1 hour in seconds
    private const CACHE_KEY = 'analytics.revenue_stats';

    /**
     * Get all revenue metrics with a single cache call
     */
    public function getStats(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return [
                'active_subscriptions' => $this->calculateActiveSubscriptions(),
            ];
        });
    }

    /**
     * Calculate active subscriptions (redeemed subscription cards)
     */
    private function calculateActiveSubscriptions(): int
    {
        return SubscriptionCard::whereNotNull('redeemed_at')
            ->whereHas('subscription', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('ending_date')
                        ->orWhere('ending_date', '>', Carbon::now());
                });
            })
            ->count();
    }

    /**
     * Clear revenue analytics cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
