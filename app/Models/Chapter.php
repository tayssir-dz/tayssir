<?php

namespace App\Models;

use App\Enums\ContentDirection;
use App\Models\Pivot\ChapterQuestion;
use App\Models\Pivot\ChapterUnit;
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
        'direction',
        'chapter_level_id',
        'active',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'direction' => ContentDirection::class,
    ];

    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('chapter_photos') ? $this->getFirstMediaUrl('chapter_photos') : null;
    }

    public function unit()
    {
        return $this->belongsToMany(Unit::class, 'chapter_unit')
            ->using(ChapterUnit::class)
            ->withPivot('sort')
            ->limit(1);
    }



    /**
     * Get the questions for the chapter.
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class)
            ->using(ChapterQuestion::class)
            ->withPivot('sort')
            ->orderBy('chapter_question.sort');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class);
    }

    /**
     * Get the effective direction based on inheritance rules.
     */
    public function getEffectiveDirection(): ContentDirection
    {
        if ($this->direction !== ContentDirection::INHERIT) {
            return $this->direction;
        }

        // Inherit from parent (Unit)
        $unit = $this->unit()->first();

        if ($unit) {
            return $unit->getEffectiveDirection();
        }

        return ContentDirection::RTL;
    }

    /**
     * Get the rtl attribute for backward compatibility.
     */
    public function getRtlAttribute()
    {
        return $this->getEffectiveDirection() === ContentDirection::RTL;
    }

    public function chapter_level()
    {
        return $this->belongsTo(ChapterLevel::class);
    }

    /**
     * Scope a query to only include active chapters.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
