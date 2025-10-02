<?php

namespace App\Console\Commands;

use App\Models\LeaderBoard;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserChapterBonus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditUserPoints extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:audit-points
        {user_id : The ID of the user to audit}
        {--fix-leaderboard : Recalculate and update leaderboard points and max_points}
        {--delete-duplicates : Delete duplicate user answer rows for exact duplicates}
        {--limit=10 : Limit for listing sample rows in findings}';

    /**
     * The console command description.
     */
    protected $description = 'Audit a user\'s answers, bonus points, leaderboard values, and max points vs subscriptions; reports duplicates and inconsistencies.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = (int) $this->argument('user_id');
        $limit = (int) $this->option('limit');

        /** @var User|null $user */
        $user = User::with(['division', 'leaderboard'])->find($userId);
        if (! $user) {
            $this->error("User with ID {$userId} not found.");
            return self::FAILURE;
        }

        // Header
        $this->info("Auditing user: {$user->name} (ID: {$user->id})");
        $this->line('');

        // 1) Core totals
        $answerPoints = (int) UserAnswer::where('user_id', $userId)->sum('points_earned');
        $bonusPoints = (int) UserChapterBonus::where('user_id', $userId)->sum('bonus_points');
        $computedTotalPoints = $answerPoints + $bonusPoints;

        /** @var LeaderBoard|null $lb */
        $lb = $user->leaderboard; // may be null
        $storedLbPoints = $lb?->points ?? null;
        $storedMaxPoints = $lb?->max_points ?? null;

        // 2) Compute accessible content ids based on current subscriptions (used by max points)
        $accessible = $this->getAccessibleIds($user);
        $accessibleMaterialIds = $accessible['materials'];
        $accessibleUnitIds = $accessible['units'];
        $accessibleChapterIds = $accessible['chapters'];

        // 3) Inaccessible answers (based on current subscriptions)
        $hasAccessible = ! empty($accessibleMaterialIds) || ! empty($accessibleUnitIds) || ! empty($accessibleChapterIds);
        if ($hasAccessible) {
            $inaccessibleAnswers = UserAnswer::where('user_id', $userId)
                ->where(function ($q) use ($accessibleMaterialIds, $accessibleUnitIds, $accessibleChapterIds) {
                    $first = true;
                    if (! empty($accessibleMaterialIds)) {
                        $q->whereNotIn('material_id', $accessibleMaterialIds);
                        $first = false;
                    }
                    if (! empty($accessibleUnitIds)) {
                        $first ? $q->whereNotIn('unit_id', $accessibleUnitIds) : $q->orWhereNotIn('unit_id', $accessibleUnitIds);
                        $first = false;
                    }
                    if (! empty($accessibleChapterIds)) {
                        $first ? $q->whereNotIn('chapter_id', $accessibleChapterIds) : $q->orWhereNotIn('chapter_id', $accessibleChapterIds);
                    }
                })
                ->select('id', 'question_id', 'chapter_id', 'unit_id', 'material_id', 'points_earned')
                ->limit($limit)
                ->get();

            $inaccessibleCount = UserAnswer::where('user_id', $userId)
                ->where(function ($q) use ($accessibleMaterialIds, $accessibleUnitIds, $accessibleChapterIds) {
                    $first = true;
                    if (! empty($accessibleMaterialIds)) {
                        $q->whereNotIn('material_id', $accessibleMaterialIds);
                        $first = false;
                    }
                    if (! empty($accessibleUnitIds)) {
                        $first ? $q->whereNotIn('unit_id', $accessibleUnitIds) : $q->orWhereNotIn('unit_id', $accessibleUnitIds);
                        $first = false;
                    }
                    if (! empty($accessibleChapterIds)) {
                        $first ? $q->whereNotIn('chapter_id', $accessibleChapterIds) : $q->orWhereNotIn('chapter_id', $accessibleChapterIds);
                    }
                })
                ->count();
        } else {
            // No accessible content -> all answers are effectively outside the current subscriptions
            $inaccessibleAnswers = UserAnswer::where('user_id', $userId)
                ->select('id', 'question_id', 'chapter_id', 'unit_id', 'material_id', 'points_earned')
                ->limit($limit)
                ->get();
            $inaccessibleCount = UserAnswer::where('user_id', $userId)->count();
        }

        // 4) Duplicate answers: exact duplicate groups on (question_id, chapter_id, unit_id, material_id)
        $exactDuplicateGroups = UserAnswer::select(
            'question_id',
            'chapter_id',
            'unit_id',
            'material_id',
            DB::raw('COUNT(*) as rows_count'),
            DB::raw('SUM(points_earned) as sum_points'),
            DB::raw('MAX(points_earned) as max_points'),
            DB::raw('MIN(points_earned) as min_points')
        )
            ->where('user_id', $userId)
            ->groupBy('question_id', 'chapter_id', 'unit_id', 'material_id')
            ->having('rows_count', '>', 1)
            ->orderByDesc('rows_count')
            ->limit($limit)
            ->get();

        $exactDuplicateCount = UserAnswer::select(DB::raw('COUNT(*) as c'))
            ->fromSub(
                UserAnswer::select(
                    'question_id',
                    'chapter_id',
                    'unit_id',
                    'material_id',
                    DB::raw('COUNT(*) as rows_count')
                )
                    ->where('user_id', $userId)
                    ->groupBy('question_id', 'chapter_id', 'unit_id', 'material_id'),
                'sub'
            )
            ->where('rows_count', '>', 1)
            ->value('c') ?? 0;

        // 5) Cross-location duplicates: same question answered in multiple chapter/unit/material
        $crossLocationQuestions = UserAnswer::select(
            'question_id',
            DB::raw("COUNT(*) as rows_count"),
            DB::raw("COUNT(DISTINCT CONCAT(chapter_id, '-', unit_id, '-', material_id)) as distinct_sets")
        )
            ->where('user_id', $userId)
            ->groupBy('question_id')
            ->having('distinct_sets', '>', 1)
            ->orderByDesc('distinct_sets')
            ->limit($limit)
            ->get();

        $crossLocationCount = UserAnswer::select(DB::raw('COUNT(*) as c'))
            ->fromSub(
                UserAnswer::select(
                    'question_id',
                    DB::raw("COUNT(DISTINCT CONCAT(chapter_id, '-', unit_id, '-', material_id)) as distinct_sets")
                )
                    ->where('user_id', $userId)
                    ->groupBy('question_id'),
                'sub'
            )
            ->where('distinct_sets', '>', 1)
            ->value('c') ?? 0;

        // 6) Recompute max points from code path
        $computedMaxPoints = (int) $user->maxPoints();

        // 6b) Compute totals LIMITED to current subscriptions for comparison (answers + bonuses in accessible chapters)
        $answerPointsWithin = 0;
        $bonusPointsWithin = 0;
        if ($hasAccessible) {
            $answerQuery = UserAnswer::where('user_id', $userId);
            if (! empty($accessibleMaterialIds)) {
                $answerQuery->whereIn('material_id', $accessibleMaterialIds);
            }
            if (! empty($accessibleUnitIds)) {
                $answerQuery->whereIn('unit_id', $accessibleUnitIds);
            }
            if (! empty($accessibleChapterIds)) {
                $answerQuery->whereIn('chapter_id', $accessibleChapterIds);
            }
            $answerPointsWithin = (int) $answerQuery->sum('points_earned');

            $bonusQuery = UserChapterBonus::where('user_id', $userId);
            if (! empty($accessibleChapterIds)) {
                $bonusQuery->whereIn('chapter_id', $accessibleChapterIds);
            } else {
                $bonusQuery->whereRaw('0=1'); // nothing accessible
            }
            $bonusPointsWithin = (int) $bonusQuery->sum('bonus_points');
        }
        $computedTotalWithin = $answerPointsWithin + $bonusPointsWithin;

        // 7) Report
        $this->table(
            ['Metric', 'Value'],
            [
                ['User', $user->name . ' (ID ' . $user->id . ')'],
                ['Division', optional($user->division)->name ?? '—'],
                ['Subscriptions', $user->subscriptions->pluck('id')->implode(', ') ?: 'None'],
                ['Answer points (sum of points_earned)', (string) $answerPoints],
                ['Bonus points', (string) $bonusPoints],
                ['Computed total points (answers + bonus)', (string) $computedTotalPoints],
                ['Computed total within CURRENT subscriptions', (string) $computedTotalWithin],
                ['Leaderboard stored points', $storedLbPoints === null ? '—' : (string) $storedLbPoints],
                ['Leaderboard stored max_points', $storedMaxPoints === null ? '—' : (string) $storedMaxPoints],
                ['Computed max_points (from subscriptions)', (string) $computedMaxPoints],
                ['Answers outside current subscriptions', (string) $inaccessibleCount],
                ['Exact duplicate answer groups', (string) $exactDuplicateCount],
                ['Cross-location duplicate questions', (string) $crossLocationCount],
            ]
        );

        // Findings
        if ($inaccessibleCount > 0) {
            $this->warn("Found {$inaccessibleCount} answer rows outside the user\'s CURRENT subscriptions.");
            $this->line('This can cause leaderboard points to exceed max_points if subscriptions were downgraded.');
            if ($inaccessibleAnswers->isNotEmpty()) {
                $this->line('Sample of inaccessible answers:');
                $this->table(['id', 'question_id', 'chapter_id', 'unit_id', 'material_id', 'points_earned'], $inaccessibleAnswers->toArray());
            }
        }

        if ($exactDuplicateCount > 0) {
            $this->warn("Found {$exactDuplicateCount} exact duplicate groups (same question/chapter/unit/material). Unique index should prevent this going forward.");
            if ($exactDuplicateGroups->isNotEmpty()) {
                $this->line('Sample duplicate groups:');
                $this->table(['question_id', 'chapter_id', 'unit_id', 'material_id', 'rows_count', 'sum_points', 'min_points', 'max_points'], $exactDuplicateGroups->toArray());
            }
        }

        if ($crossLocationCount > 0) {
            $this->warn("Found {$crossLocationCount} questions answered in multiple locations (different chapter/unit/material). Check question-chapter mapping.");
            $this->table(['question_id', 'rows_count', 'distinct_sets'], $crossLocationQuestions->toArray());
        }

        // Consistency checks
        $this->line('');
        $this->info('Consistency checks:');
        $mismatches = [];

        if ($storedLbPoints !== null && $storedLbPoints !== $computedTotalPoints) {
            $mismatches[] = "Leaderboard points (stored={$storedLbPoints}) differ from computed total ({$computedTotalPoints}).";
        }
        if ($storedMaxPoints !== null && $storedMaxPoints !== $computedMaxPoints) {
            $mismatches[] = "Leaderboard max_points (stored={$storedMaxPoints}) differ from computed ({$computedMaxPoints}).";
        }
        if ($computedTotalPoints > $computedMaxPoints) {
            $mismatches[] = "Computed total points ({$computedTotalPoints}) exceed computed max_points ({$computedMaxPoints}). Likely due to answers from content outside current subscriptions.";
        }

        if (empty($mismatches)) {
            $this->info('✓ No inconsistencies detected.');
        } else {
            foreach ($mismatches as $m) {
                $this->warn('• ' . $m);
            }
        }

        // Optional fixes
        if ($this->option('delete-duplicates') && $exactDuplicateCount > 0) {
            $this->line('');
            if ($this->confirm("Delete exact duplicate rows now? I will keep one row per (question,chapter,unit,material) with the highest points_earned.", false)) {
                $deleted = $this->deleteExactDuplicates($userId);
                $this->info("Deleted {$deleted} duplicate rows.");
            }
        }

        if ($this->option('fix-leaderboard')) {
            $this->line('');
            $leaderboard = LeaderBoard::updateOrCreate(
                ['user_id' => $userId],
                [
                    'points' => $computedTotalPoints,
                    'max_points' => $computedMaxPoints,
                    'last_updated_at' => now(),
                ]
            );
            $this->info('Leaderboard updated: points=' . $leaderboard->points . ', max_points=' . $leaderboard->max_points);
        }

        // Summary Guidance
        $this->line('');
        $this->line('Notes:');
        $this->line('- points are calculated from ALL answers + bonuses.');
        $this->line('- max_points are calculated from CURRENT subscriptions (accessible content only).');
        $this->line('If a user downgrades subscriptions, points can exceed max_points. Consider:');
        $this->line('  • Showing progress as min(points, max_points) / max_points, or');
        $this->line('  • Filtering points to current subscriptions when updating leaderboard, if that matches business rules.');

        return self::SUCCESS;
    }

    private function getAccessibleIds(User $user): array
    {
        if (! $user->division || $user->subscriptions->isEmpty()) {
            return [
                'materials' => [],
                'units' => [],
                'chapters' => [],
            ];
        }

        $subscriptionIds = $user->subscriptions->pluck('id');

        $materials = $user->division->materials()
            ->whereHas('units', function ($query) use ($subscriptionIds) {
                $query->whereHas('subscriptions', function ($subQuery) use ($subscriptionIds) {
                    $subQuery->whereIn('subscriptions.id', $subscriptionIds);
                });
            })
            ->with(['units' => function ($query) use ($subscriptionIds) {
                $query->whereHas('subscriptions', function ($subQuery) use ($subscriptionIds) {
                    $subQuery->whereIn('subscriptions.id', $subscriptionIds);
                })->with(['chapters' => function ($chapterQuery) use ($subscriptionIds) {
                    $chapterQuery->whereHas('subscriptions', function ($subQuery) use ($subscriptionIds) {
                        $subQuery->whereIn('subscriptions.id', $subscriptionIds);
                    });
                }]);
            }])
            ->get();

        return [
            'materials' => $materials->pluck('id')->values()->all(),
            'units' => $materials->flatMap->units->pluck('id')->values()->all(),
            'chapters' => $materials->flatMap->units->flatMap->chapters->pluck('id')->values()->all(),
        ];
    }

    private function deleteExactDuplicates(int $userId): int
    {
        // Find exact duplicate groups and delete older/extra rows keeping the one with highest points_earned and latest updated_at
        $groups = UserAnswer::select(
            'question_id',
            'chapter_id',
            'unit_id',
            'material_id',
            DB::raw('COUNT(*) as rows_count')
        )
            ->where('user_id', $userId)
            ->groupBy('question_id', 'chapter_id', 'unit_id', 'material_id')
            ->having('rows_count', '>', 1)
            ->get();

        $deleted = 0;

        foreach ($groups as $g) {
            $rows = UserAnswer::where('user_id', $userId)
                ->where('question_id', $g->question_id)
                ->where('chapter_id', $g->chapter_id)
                ->where('unit_id', $g->unit_id)
                ->where('material_id', $g->material_id)
                ->orderByDesc('points_earned')
                ->orderByDesc('updated_at')
                ->get();

            // Keep the first row, delete the rest
            $toDelete = $rows->skip(1)->pluck('id');
            if ($toDelete->isNotEmpty()) {
                $deleted += UserAnswer::whereIn('id', $toDelete)->delete();
            }
        }

        return $deleted;
    }
}
