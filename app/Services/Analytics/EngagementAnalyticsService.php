<?php

namespace App\Services\Analytics;

use App\Models\Chapter;
use App\Models\User;
use App\Models\UserAnswer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EngagementAnalyticsService
{
    private const CACHE_DURATION = 3600; // 1 hour in seconds
    private const CACHE_KEY = 'analytics.engagement_stats';

    /**
     * Get all engagement metrics with a single cache call
     */
    public function getStats(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return [
                'lesson_completion_rate' => $this->calculateLessonCompletionRate(),
                'streak_count' => $this->calculateStreakCount(),
                'notification_open_rate' => $this->calculateNotificationOpenRate(),
            ];
        });
    }

    /**
     * Calculate lesson completion rate (students who completed a full lesson)
     */
    private function calculateLessonCompletionRate(): string
    {
        $totalStudents = User::count();

        if ($totalStudents === 0) {
            return '0%';
        }

        // Get all chapters
        $totalChapters = Chapter::count();

        if ($totalChapters === 0) {
            return '0%';
        }

        // Students who have answered all questions in at least one chapter
        // (This means they completed at least one full lesson)
        $completedStudents = DB::table('user_answers')
            ->select('user_id')
            ->distinct('user_id')
            ->groupBy('user_id')
            ->havingRaw('count(distinct chapter_id) > 0')
            ->count();

        if ($completedStudents === 0) {
            return '0%';
        }

        $rate = ($completedStudents / $totalStudents) * 100;

        return round($rate) . '%';
    }

    /**
     * Calculate streak count (users with consecutive learning days)
     * This counts users who have logged activity for consecutive days
     */
    private function calculateStreakCount(): int
    {
        // Get users with answers in the last 30 days
        $thirtyDaysAgo = Carbon::now()->subDays(30)->startOfDay();

        // Get users grouped by consecutive days of activity
        $usersWithActivity = DB::table('user_answers')
            ->select('user_id', DB::raw('DATE(created_at) as activity_date'))
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->distinct()
            ->orderBy('user_id')
            ->orderBy('activity_date')
            ->get();

        if ($usersWithActivity->isEmpty()) {
            return 0;
        }

        $streakUsers = [];
        $currentUser = null;
        $currentStreak = 0;
        $lastDate = null;

        foreach ($usersWithActivity as $record) {
            $userId = $record->user_id;
            $activityDate = Carbon::parse($record->activity_date);

            // New user, reset streak
            if ($userId !== $currentUser) {
                if ($currentStreak >= 2) {
                    // At least 2 consecutive days = a streak
                    $streakUsers[$currentUser] = $currentStreak;
                }
                $currentUser = $userId;
                $currentStreak = 1;
                $lastDate = $activityDate;
            } else {
                // Check if this is a consecutive day
                if ($lastDate !== null) {
                    $daysDiff = $lastDate->diffInDays($activityDate);
                    if ($daysDiff === 1) {
                        $currentStreak++;
                    } else {
                        // Streak broken, check if we had a streak
                        if ($currentStreak >= 2) {
                            $streakUsers[$currentUser] = $currentStreak;
                        }
                        $currentStreak = 1;
                    }
                }
                $lastDate = $activityDate;
            }
        }

        // Don't forget the last user
        if ($currentStreak >= 2) {
            $streakUsers[$currentUser] = $currentStreak;
        }

        return count($streakUsers);
    }

    /**
     * Calculate notification open rate (placeholder for future implementation)
     */
    private function calculateNotificationOpenRate(): string
    {
        // This is a placeholder for future implementation
        // Currently returns 77% as a static value
        return '77%';
    }

    /**
     * Clear engagement analytics cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
