<?php

namespace App\Models;

use App\Enums\QuestionDifficulty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Chapter extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'unit_id',
        // 'photo_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function chapter_units()
    {
        return $this->belongsToMany(Unit::class, 'chapter_unit')
            ->withPivot('sort')
            ->orderBy('chapter_unit.sort');
    }

    public function getUnitAttribute()
    {
        return $this->chapter_units()->first();
    }



    /**
     * Get the questions for the chapter.
     */

    public function questions()
    {
        return $this->belongsToMany(Question::class)
            ->using(ChapterQuestion::class)
            ->withPivot('sort')
            ->orderBy('chapter_question.sort')
            ->orderBy('questions.id');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class);
    }

    public function distributeDifficulties()
    {
        $questions = $this->questions()->withPivot('sort')->orderBy('chapter_question.sort')->get();
        $totalQuestions = $questions->count();

        if ($totalQuestions === 0) {
            return $this;
        }

        if ($totalQuestions <= 3) {
            $easyCount = 1;
            $mediumCount = $totalQuestions > 1 ? 1 : 0;
            $hardCount = $totalQuestions > 2 ? 1 : 0;
        } else {
            // Ensure even distribution for more than 3 questions
            $easyCount = (int) ceil($totalQuestions / 3);
            $remainingQuestions = $totalQuestions - $easyCount;
            $mediumCount = (int) ceil($remainingQuestions / 2);
            $hardCount = $totalQuestions - $easyCount - $mediumCount;
        }

        $currentIndex = 0;

        // Assign easy questions
        for ($i = 0; $i < $easyCount; $i++) {
            $questions[$currentIndex++]->update(['difficulty' => QuestionDifficulty::EASY]);
        }

        // Assign medium questions
        for ($i = 0; $i < $mediumCount; $i++) {
            $questions[$currentIndex++]->update(['difficulty' => QuestionDifficulty::MEDIUM]);
        }

        // Assign hard questions
        while ($currentIndex < $totalQuestions) {
            $questions[$currentIndex++]->update(['difficulty' => QuestionDifficulty::HARD]);
        }

        return $this;
    }
}
