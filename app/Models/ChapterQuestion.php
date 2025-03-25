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
}
