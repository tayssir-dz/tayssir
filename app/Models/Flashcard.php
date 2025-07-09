<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'flashcard_group_id',
    ];

    /**
     * Get the flashcard group that owns the flashcard.
     */
    public function flashcardGroup()
    {
        return $this->belongsTo(FlashcardGroup::class);
    }
}
