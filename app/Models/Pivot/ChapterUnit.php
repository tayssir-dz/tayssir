<?php

namespace App\Models\Pivot;

use App\Models\Chapter;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ChapterUnit extends Pivot
{
    protected $table = 'chapter_unit';

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
