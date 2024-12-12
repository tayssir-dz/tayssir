<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ChapterQuestion extends Pivot
{
    protected $table = 'chapter_question';

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    protected static function booted()
    {
        static::saved(function ($chapterQuestion) {
            if ($chapterQuestion->isDirty('sort')) {
                $chapterQuestion->chapter->distributeDifficulties();
            }
        });
    }
}
