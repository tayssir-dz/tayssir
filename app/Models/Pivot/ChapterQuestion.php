<?php

namespace App\Models\Pivot;

use App\Models\Chapter;
use App\Models\Question;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ChapterQuestion extends Pivot
{
    protected $table = 'chapter_question';

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
