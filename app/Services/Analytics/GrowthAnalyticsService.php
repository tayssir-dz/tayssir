<?php

namespace App\Services\Analytics;

use App\Models\PromoCode;
use App\Models\ReferralSource;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserAnswer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GrowthAnalyticsService
{
    private const CACHE_DURATION = 3600; // 1 hour in seconds
    private const CACHE_KEY = 'analytics.growth_stats';

    /**
     * Get all growth metrics with a single cache call
     */
    public function getStats(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return [
                'new_signups_by_source' => $this->calculateNewSignupsBySource(),
                'activation_rate' => $this->calculateActivationRate(),
                'conversion_rate' => $this->calculateConversionRate(),
                'top_referrer' => $this->calculateTopReferrer(),
            ];
        });
    }

    /**
     * Calculate new signups by referral source this week
     */
    private function calculateNewSignupsBySource(): array
    {
        $thisWeekStart = Carbon::now()->startOfWeek();

        $sources = ReferralSource::withCount([
            'users' => function ($query) use ($thisWeekStart) {
                $query->where('created_at', '>=', $thisWeekStart);
            },
        ])
            ->orderByDesc('users_count')
            ->limit(2)
            ->pluck('name', 'users_count')
            ->toArray();

        // Format as "Source: count" pairs
        $formatted = [];
        foreach ($sources as $count => $name) {
            $formatted[] = [
                'source' => $name,
                'count' => $count,
            ];
        }

        return $formatted;
    }

    /**
     * Calculate activation rate (new users who completed first lesson or exercise)
     */
    private function calculateActivationRate(): string
    {
        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->startOfWeek())->count();

        if ($newUsersThisWeek === 0) {
            return '0%';
        }

        // Users who completed at least one answer this week
        $activatedUsers = UserAnswer::whereIn('user_id', function ($query) {
            $query->select('id')
                ->from('users')
                ->where('created_at', '>=', Carbon::now()->startOfWeek());
        })
            ->distinct('user_id')
            ->count('user_id');

        $rate = ($activatedUsers / $newUsersThisWeek) * 100;

        return round($rate) . '%';
    }

    /**
     * Calculate conversion rate from free to paid
     */
    private function calculateConversionRate(): string
    {
        $totalUsers = User::count();

        if ($totalUsers === 0) {
            return '0%';
        }

        // Users with redeemed subscription cards (indicating paid subscription)
        $paidUsers = DB::table('subscription_cards')
            ->whereNotNull('redeemed_at')
            ->distinct('user_id')
            ->count('user_id');

        $rate = ($paidUsers / $totalUsers) * 100;

        return round($rate, 1) . '%';
    }

    /**
     * Calculate top referrer (most used promo code this week)
     */
    private function calculateTopReferrer(): array
    {
        $thisWeekStart = Carbon::now()->startOfWeek();

        $topCode = PromoCode::select('code')
            ->withCount([
                'manualPayments' => function ($query) use ($thisWeekStart) {
                    $query->where('created_at', '>=', $thisWeekStart);
                },
            ])
            ->orderByDesc('manual_payments_count')
            ->first();

        if (!$topCode || $topCode->manual_payments_count === 0) {
            return [
                'code' => 'N/A',
                'count' => 0,
            ];
        }

        return [
            'code' => $topCode->code,
            'count' => $topCode->manual_payments_count,
        ];
    }

    /**
     * Clear growth analytics cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
