<?php

namespace App\Traits;

use App\Enums\ChapterVisibility;
use App\Models\Chapter;
use App\Models\Material;
use App\Models\Question;
use App\Models\Unit;
use App\Models\UserAnswer;
use App\Models\UserChapterBonus;
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
            $q->whereHas('unit', function ($q) use ($material) {
                $q->whereHas('material', function ($q) use ($material) {
                    $q->where('materials.id', $material->id);
                });
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

        $totalQuestions = Question::whereHas('chapter', function ($q) use ($unit) {
            $q->whereHas('unit', function ($q) use ($unit) {
                $q->where('units.id', $unit->id);
            });
        })->count();

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
     * Calculate all points earned by the user efficiently, including bonus points
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

        // Get bonus points in a single query
        $bonusPoints = UserChapterBonus::select('chapter_id', 'bonus_points')
            ->where('user_id', $this->id)
            ->whereIn('chapter_id', $chapterIds)
            ->get();

        // Create a map of bonus points by chapter
        $bonusPointsByChapter = [];
        foreach ($bonusPoints as $bonus) {
            $bonusPointsByChapter[$bonus->chapter_id] = $bonus->bonus_points;
        }

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

        // Add bonus points to the respective chapter, unit, and material
        $bonusTotal = 0;
        foreach ($bonusPointsByChapter as $chapterId => $bonus) {
            if ($bonus > 0) {
                $bonusTotal += $bonus;

                // Add bonus to chapter points
                if (!isset($chapterPoints[$chapterId])) {
                    $chapterPoints[$chapterId] = 0;
                }
                $chapterPoints[$chapterId] += $bonus;

                // Find the related unit and material for this chapter
                $chapterInfo = null;
                foreach ($materials as $material) {
                    foreach ($material->units as $unit) {
                        foreach ($unit->chapters as $chapter) {
                            if ($chapter->id == $chapterId) {
                                // Add bonus to unit points
                                if (!isset($unitPoints[$unit->id])) {
                                    $unitPoints[$unit->id] = 0;
                                }
                                $unitPoints[$unit->id] += $bonus;

                                // Add bonus to material points
                                if (!isset($materialPoints[$material->id])) {
                                    $materialPoints[$material->id] = 0;
                                }
                                $materialPoints[$material->id] += $bonus;
                                break 3;
                            }
                        }
                    }
                }
            }
        }

        $totalPoints += $bonusTotal;

        return [
            'total' => $totalPoints,
            'materials' => $materialPoints,
            'units' => $unitPoints,
            'chapters' => $chapterPoints,
            'bonuses' => $bonusPointsByChapter
        ];
    }

    /**
     * Get the total points earned by the user, including bonus points
     *
     * @return int
     */
    public function points(): int
    {
        $answerPoints = UserAnswer::where('user_id', $this->id)
            ->sum('points_earned');

        $bonusPoints = UserChapterBonus::where('user_id', $this->id)
            ->sum('bonus_points');

        return $answerPoints + $bonusPoints;
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
     * Get the points earned by the user for a specific chapter, including bonus
     *
     * @param mixed $chapter
     * @return int
     */
    public function chapterPoints($chapter): int
    {
        $answerPoints = UserAnswer::where('user_id', $this->id)
            ->where('chapter_id', $chapter->id)
            ->sum('points_earned');

        $bonusPoints = UserChapterBonus::where('user_id', $this->id)
            ->where('chapter_id', $chapter->id)
            ->value('bonus_points') ?? 0;

        return $answerPoints + $bonusPoints;
    }

    /**
     * Get the chapter visibility status for a specific unit
     * 
     * @param int $unitId
     * @return array
     */
    public function getChapterVisibility(int $unitId): array
    {
        // Get all chapters in the unit in their proper sequence
        $chapters = Chapter::whereHas('unit', function ($query) use ($unitId) {
            $query->where('units.id', $unitId);
        })
            ->get()
            ->sortBy(function ($chapter) {
                return $chapter->unit()->first()->pivot->sort;
            });

        if ($chapters->isEmpty()) {
            return [];
        }

        // Get user answers for these chapters
        $userAnswers = UserAnswer::where('user_id', $this->id)
            ->whereIn('chapter_id', $chapters->pluck('id'))
            ->select('chapter_id')
            ->distinct()
            ->get()
            ->pluck('chapter_id')
            ->toArray();

        $result = [];
        $foundCurrent = false;

        foreach ($chapters as $chapter) {
            $status = ChapterVisibility::LOCKED;

            // If the chapter has answers, it's DONE
            if (in_array($chapter->id, $userAnswers)) {
                $status = ChapterVisibility::DONE;
            }
            // If we haven't found the current chapter yet and this one is not DONE, it's CURRENT
            elseif (!$foundCurrent) {
                $status = ChapterVisibility::CURRENT;
                $foundCurrent = true;
            }

            $result[$chapter->id] = $status->value;
        }

        // If no CURRENT chapter was set and it's the first chapter, make it CURRENT
        if (!$foundCurrent && !empty($result)) {
            $firstChapterId = $chapters->first()->id;
            $result[$firstChapterId] = ChapterVisibility::CURRENT->value;
        }

        return $result;
    }
}
