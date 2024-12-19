<?php

namespace App\Traits;

use App\Models\Question;
use App\Models\UserAnswer;


trait HasProgress
{
    public function materialProgress($material)
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

    public function unitProgress($unit)
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

    public function chapterProgress($chapter)
    {
        $answers = UserAnswer::where('user_id', $this->id)
            ->where('chapter_id', $chapter->id)
            ->count();

        $totalQuestions = $chapter->questions()->count();

        return $totalQuestions > 0 ? ($answers / $totalQuestions) * 100 : 0;
    }
}
