<?php

namespace App\Models;

use App\Enums\ContentDirection;
use App\Enums\QuestionScope;
use App\Enums\QuestionType;
use App\Models\Pivot\ChapterQuestion;
use App\Traits\User\HasQuestionMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Question extends Model implements HasMedia
{
    use HasFactory;
    use HasQuestionMedia;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question',
        'hint',
        'explanation_text',
        'options',
        'question_type',
        'scope',
        'direction',
        'question_is_latex',
        'explanation_text_is_latex',
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
     * Get the chapter that owns the question.
     */

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, mixed>
     */
    protected $casts = [
        'options' => 'array',
        'hint' => 'array',
        'question_type' => QuestionType::class,
        'scope' => QuestionScope::class,
        'direction' => ContentDirection::class,
        'question_is_latex' => 'boolean',
        'explanation_text_is_latex' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
        $this->addMediaCollection('explanation_asset')
            ->singleFile();
        $this->addMediaCollection('hint_image')
            ->singleFile();
    }

    public function getImageAttribute()
    {
        return $this->hasMedia('image') ? $this->getFirstMediaUrl('image') : null;
    }

    public function getExplanationAssetAttribute()
    {
        return $this->hasMedia('explanation_asset') ? $this->getFirstMediaUrl('explanation_asset') : null;
    }

    public function getHintImageAttribute()
    {
        return $this->hasMedia('hint_image') ? $this->getFirstMediaUrl('hint_image') : null;
    }

    public function chapter()
    {
        return $this->belongsToMany(related: Chapter::class)
            ->using(ChapterQuestion::class)
            ->withPivot('sort')  // Add any pivot columns you need
            ->limit(1);  // Ensure only one chapter is returned
    }

    public function getChapterNameAttribute(): ?string
    {
        return $this->chapter()->first()?->name;
    }

    /**
     * Get the effective direction based on inheritance rules.
     */
    public function getEffectiveDirection(): ContentDirection
    {
        if ($this->direction !== ContentDirection::INHERIT) {
            return $this->direction;
        }

        // Inherit from parent (Chapter)
        $chapter = $this->chapter()->first();

        if ($chapter) {
            return $chapter->getEffectiveDirection();
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

    public function getPointsAttribute()
    {
        $chapter = $this->chapter()->first();
        if (! $chapter || ! $chapter->chapter_level) {
            return 0;
        }

        return match ($this->scope) {
            QuestionScope::EXERCICE => $chapter->chapter_level->exercice_points,
            QuestionScope::LESSON => $chapter->chapter_level->lesson_points,
        };
    }
}
