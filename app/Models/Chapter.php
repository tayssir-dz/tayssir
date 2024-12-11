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

    /**
     * Get the unit that owns the chapter.
     */

    public function unit()
    {
        return $this->belongsTo(Unit::class);
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

        $easyCount = (int) ceil($totalQuestions / 3);
        $mediumCount = (int) ceil($totalQuestions / 3);
        $hardCount = $totalQuestions - $easyCount - $mediumCount;

        $questions->each(function ($question, $index) use ($easyCount, $mediumCount) {
            if ($index < $easyCount) {
                $difficulty = QuestionDifficulty::EASY;
            } elseif ($index < ($easyCount + $mediumCount)) {
                $difficulty = QuestionDifficulty::MEDIUM;
            } else {
                $difficulty = QuestionDifficulty::HARD;
            }

            $question->update(['difficulty' => $difficulty]);
        });

        return $this;
    }
}
