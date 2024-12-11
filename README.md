<img alt="tayssir logo" src="public/tayssir.svg">

---

This app provides a comprehensive platform
for
baccalaureate students to practice quizzes
and enhance their knowledge. With a user-friendly interface and a wide range of subjects to choose from,
students can test their understanding and track their progress. Join now and boost your exam preparation
with our interactive quizzes!The app provides a comprehensive platform for baccalaureate students to
practice quizzes and enhance their knowledge. With a user-friendly interface and a wide range of subjects to
choose from, students can test their understanding and track their progress. Join now and boost your exam
preparation with our interactive quizzes!

---

I'll help you implement this feature. Here's a step-by-step solution:

Create a QuestionDifficulty enum
Create a UserProgress model to track progress
Add methods to calculate scores based on difficulty
Modify the Question model to include difficulty
Create a UserAnswer model to track answers
Here are the necessary code changes:

[app/Enums/QuestionDifficulty.php]

```php
<?php

namespace App\Enums;

enum QuestionDifficulty: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    public function points(): int
    {
        return match($this) {
            self::EASY => 1,
            self::MEDIUM => 2,
            self::HARD => 3,
        };
    }
}
```

Create a new enum for question difficulty levels:

[app/Models/Question.php]

```php
<?php
use App\Enums\QuestionDifficulty;

class Question extends Model implements HasMedia
{
    // ...existing code...

    protected $fillable = [
        'question',
        'question_type',
        'options',
        'points',
        'hint',
        'difficulty',  // Add this
    ];

    protected $casts = [
        'options' => 'array',
        'question_type' => QuestionType::class,
        'difficulty' => QuestionDifficulty::class,  // Add this
    ];

    public static function assignDifficulties($chapterId)
    {
        $questions = self::whereHas('chapter', function($q) use ($chapterId) {
            $q->where('chapters.id', $chapterId);
        })->orderBy('chapter_question.sort')->get();

        $total = $questions->count();
        $easyCount = floor($total / 3);
        $mediumCount = floor($total / 3);
        $hardCount = $total - $easyCount - $mediumCount;

        $questions->each(function($question, $index) use ($easyCount, $mediumCount) {
            if ($index < $easyCount) {
                $question->difficulty = QuestionDifficulty::EASY;
            } elseif ($index < ($easyCount + $mediumCount)) {
                $question->difficulty = QuestionDifficulty::MEDIUM;
            } else {
                $question->difficulty = QuestionDifficulty::HARD;
            }
            $question->save();
        });
    }
}
```

Add difficulty field and helper methods:

[app/Models/UserProgress.php]

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chapter_id',
        'total_points',
        'completed_questions',
        'total_questions',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
```

Create a new model to track user progress:

[app/Models/UserAnswer.php]

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'answer',
        'is_correct',
        'points_earned',
    ];

    protected $casts = [
        'answer' => 'array',
        'is_correct' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
```

Create a new model to track user answers:

Database Migrations
You'll need to create migrations for the new tables and columns. Here are the key commands:

```
php artisan make:migration add_difficulty_to_questions_table
php artisan make:migration create_user_progress_table
php artisan make:migration create_user_answers_table
```

This implementation:

Tracks user progress at the chapter level
Assigns difficulties automatically based on question order
Calculates points based on difficulty
Stores individual answers for review
Allows for efficient progress calculation
You can then submit answers and update progress using:
