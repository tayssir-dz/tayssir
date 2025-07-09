<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashcardGroup extends Model
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
        'material_id',
    ];

    /**
     * Get the material that owns the flashcard group.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the flashcards for the flashcard group.
     */
    public function flashcards()
    {
        return $this->hasMany(Flashcard::class);
    }
}
