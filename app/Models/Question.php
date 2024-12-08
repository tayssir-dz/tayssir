<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Enums\QuestionType;

class Question extends Model implements HasMedia
{
    use InteractsWithMedia;

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'question',
        'question_type',
        'options',
        "points",
        "hint",
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
        'question_type' => QuestionType::class,
    ];

    public function chapter()
    {
        return $this->belongsToMany(Chapter::class)
            ->withPivot('sort')  // Add any pivot columns you need
            ->limit(1);  // Ensure only one chapter is returned
    }
}
