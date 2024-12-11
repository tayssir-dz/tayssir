<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Cache;

class ChapterQuestion extends Pivot
{
    protected $table = 'chapter_question';

    protected static function booted()
    {
        static::updated(function ($pivot) {
            if ($pivot->isDirty('sort')) {
                $chapterId = $pivot->chapter_id;
                $cacheKey = "chapter_{$chapterId}_sort_update";

                // Only schedule distribution if not already scheduled
                if (!Cache::has($cacheKey)) {
                    Cache::put($cacheKey, true, now()->addSeconds(5));

                    // Schedule the distribution after all sorts are done
                    dispatch(function () use ($chapterId, $cacheKey) {
                        $chapter = Chapter::find($chapterId);
                        $chapter?->distributeDifficulties();
                        Cache::forget($cacheKey);
                    })->delay(now()->addSeconds(5));
                }
            }
        });
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
