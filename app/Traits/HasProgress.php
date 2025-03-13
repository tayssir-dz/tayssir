<?php

namespace App\Traits;

use App\Models\Chapter;
use App\Models\Material;
use App\Models\Question;
use App\Models\Unit;
use App\Models\UserAnswer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait HasProgress
{
    /**
     * Get user progress for a specific material
     */
    public function materialProgress($material): int
    {
        $answers = UserAnswer::where('user_id', $this->id)
            ->where('material_id', $material->id)
            ->count();

        $totalQuestions = Question::whereHas('chapter', function ($q) use ($material) {
            $q->whereHas('chapter_units.material_units', function ($q) use ($material) {
                $q->where('materials.id', $material->id);
            });
        })->count();

        return $totalQuestions > 0 ? ($answers / $totalQuestions) * 100 : 0;
    }

    /**
     * Get user progress for a specific unit
     */
    public function unitProgress($unit): int
    {
        $answers = UserAnswer::where('user_id', $this->id)
            ->where('unit_id', $unit->id)
            ->count();

        $totalQuestions = Question::whereHas(
            'chapter.chapter_units',
            fn($q) => $q->where('units.id', $unit->id)
        )->count();

        return $totalQuestions > 0 ? ($answers / $totalQuestions) * 100 : 0;
    }

    /**
     * Get user progress for a specific chapter
     */
    public function chapterProgress($chapter): int
    {
        $answers = UserAnswer::where('user_id', $this->id)
            ->where('chapter_id', $chapter->id)
            ->count();

        $totalQuestions = $chapter->questions()->count();

        return $totalQuestions > 0 ? ($answers / $totalQuestions) * 100 : 0;
    }

    /**
     * Get user progress for all materials in a more efficient way
     */
    public function MaterialsProgress()
    {
        $materials = $this->division->materials()->with(['units.chapters.questions'])->get();

        return $materials->map(function ($material) {
            $totalQuestions = $material->units
                ->flatMap(fn($unit) => $unit->chapters)
                ->flatMap(fn($chapter) => $chapter->questions)
                ->count();

            $answers = UserAnswer::where('user_id', $this->id)
                ->where('material_id', $material->id)
                ->count();

            return [
                "material_id" => $material->id,
                "progress" => $totalQuestions > 0 ? ($answers / $totalQuestions) * 100 : 0
            ];
        })->toArray();
    }

    /**
     * Get all progress data efficiently for all accessible materials, units, and chapters
     * 
     * @return array
     */
    public function getAllProgressData(): array
    {
        $subscriptionIds = $this->subscriptions->pluck('id');

        // Get all accessible entities
        $materials = $this->division->materials()
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
                    })->with('questions');
                }]);
            }])
            ->get();

        // Calculate question counts for each entity
        $materialQuestionCounts = [];
        $unitQuestionCounts = [];
        $chapterQuestionCounts = [];

        // Extract IDs for efficient querying
        $materialIds = [];
        $unitIds = [];
        $chapterIds = [];

        foreach ($materials as $material) {
            $materialIds[] = $material->id;
            $materialQuestionCount = 0;

            foreach ($material->units as $unit) {
                $unitIds[] = $unit->id;
                $unitQuestionCount = 0;

                foreach ($unit->chapters as $chapter) {
                    $chapterIds[] = $chapter->id;
                    $chapterQuestionCount = $chapter->questions->count();

                    $chapterQuestionCounts[$chapter->id] = $chapterQuestionCount;
                    $unitQuestionCount += $chapterQuestionCount;
                }

                $unitQuestionCounts[$unit->id] = $unitQuestionCount;
                $materialQuestionCount += $unitQuestionCount;
            }

            $materialQuestionCounts[$material->id] = $materialQuestionCount;
        }

        // Get user answers in a single query
        $userAnswers = UserAnswer::select(
            'material_id',
            'unit_id',
            'chapter_id',
            DB::raw('COUNT(*) as answer_count')
        )
            ->where('user_id', $this->id)
            ->whereIn('material_id', $materialIds)
            ->whereIn('unit_id', $unitIds)
            ->whereIn('chapter_id', $chapterIds)
            ->groupBy('material_id', 'unit_id', 'chapter_id')
            ->get();

        // Organize answers by entity
        $materialAnswers = [];
        $unitAnswers = [];
        $chapterAnswers = [];

        foreach ($userAnswers as $answer) {
            if (isset($answer->material_id)) {
                if (!isset($materialAnswers[$answer->material_id])) {
                    $materialAnswers[$answer->material_id] = 0;
                }
                $materialAnswers[$answer->material_id] += $answer->answer_count;
            }

            if (isset($answer->unit_id)) {
                if (!isset($unitAnswers[$answer->unit_id])) {
                    $unitAnswers[$answer->unit_id] = 0;
                }
                $unitAnswers[$answer->unit_id] += $answer->answer_count;
            }

            if (isset($answer->chapter_id)) {
                $chapterAnswers[$answer->chapter_id] = $answer->answer_count;
            }
        }

        // Calculate progress percentages
        $materialProgress = [];
        foreach ($materialIds as $id) {
            $totalQuestions = $materialQuestionCounts[$id] ?? 0;
            $answered = $materialAnswers[$id] ?? 0;
            $materialProgress[$id] = $totalQuestions > 0 ? ($answered / $totalQuestions) * 100 : 0;
        }

        $unitProgress = [];
        foreach ($unitIds as $id) {
            $totalQuestions = $unitQuestionCounts[$id] ?? 0;
            $answered = $unitAnswers[$id] ?? 0;
            $unitProgress[$id] = $totalQuestions > 0 ? ($answered / $totalQuestions) * 100 : 0;
        }

        $chapterProgress = [];
        foreach ($chapterIds as $id) {
            $totalQuestions = $chapterQuestionCounts[$id] ?? 0;
            $answered = $chapterAnswers[$id] ?? 0;
            $chapterProgress[$id] = $totalQuestions > 0 ? ($answered / $totalQuestions) * 100 : 0;
        }

        return [
            'materials' => $materialProgress,
            'units' => $unitProgress,
            'chapters' => $chapterProgress,
            'points' => $this->calculateAllPoints()
        ];
    }

    /**
     * Calculate all points earned by the user efficiently
     * 
     * @return array
     */
    protected function calculateAllPoints(): array
    {
        $subscriptionIds = $this->subscriptions->pluck('id');

        // Get all accessible materials, units and chapters IDs
        $materials = $this->division->materials()
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

        $materialIds = $materials->pluck('id')->toArray();
        $unitIds = $materials->flatMap->units->pluck('id')->toArray();
        $chapterIds = $materials->flatMap->units->flatMap->chapters->pluck('id')->toArray();

        // Get points in a single query grouped by material, unit, and chapter
        $userPoints = UserAnswer::select(
            DB::raw('SUM(points_earned) as total_points'),
            'material_id',
            'unit_id',
            'chapter_id'
        )
            ->where('user_id', $this->id)
            ->whereIn('material_id', $materialIds)
            ->groupBy('material_id', 'unit_id', 'chapter_id')
            ->get();

        // Calculate points by material, unit, and chapter
        $materialPoints = [];
        $unitPoints = [];
        $chapterPoints = [];
        $totalPoints = 0;

        foreach ($userPoints as $points) {
            $totalPoints += $points->total_points;

            if (isset($points->material_id)) {
                if (!isset($materialPoints[$points->material_id])) {
                    $materialPoints[$points->material_id] = 0;
                }
                $materialPoints[$points->material_id] += $points->total_points;
            }

            if (isset($points->unit_id)) {
                if (!isset($unitPoints[$points->unit_id])) {
                    $unitPoints[$points->unit_id] = 0;
                }
                $unitPoints[$points->unit_id] += $points->total_points;
            }

            if (isset($points->chapter_id)) {
                if (!isset($chapterPoints[$points->chapter_id])) {
                    $chapterPoints[$points->chapter_id] = 0;
                }
                $chapterPoints[$points->chapter_id] += $points->total_points;
            }
        }

        return [
            'total' => $totalPoints,
            'materials' => $materialPoints,
            'units' => $unitPoints,
            'chapters' => $chapterPoints
        ];
    }

    /**
     * Get the total points earned by the user
     *
     * @return int
     */
    public function points(): int
    {
        return UserAnswer::where('user_id', $this->id)
            ->sum('points_earned');
    }

    /**
     * Get the points earned by the user for a specific material
     *
     * @param mixed $material
     * @return int
     */
    public function materialPoints($material): int
    {
        return UserAnswer::where('user_id', $this->id)
            ->where('material_id', $material->id)
            ->sum('points_earned');
    }

    /**
     * Get the points earned by the user for a specific unit
     *
     * @param mixed $unit
     * @return int
     */
    public function unitPoints($unit): int
    {
        return UserAnswer::where('user_id', $this->id)
            ->where('unit_id', $unit->id)
            ->sum('points_earned');
    }

    /**
     * Get the points earned by the user for a specific chapter
     *
     * @param mixed $chapter
     * @return int
     */
    public function chapterPoints($chapter): int
    {
        return UserAnswer::where('user_id', $this->id)
            ->where('chapter_id', $chapter->id)
            ->sum('points_earned');
    }
}
