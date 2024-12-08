<?php

namespace App\Models;

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
            ->withPivot('sort')
            ->orderBy('chapter_question.sort');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class);
    }

}
