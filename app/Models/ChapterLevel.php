<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChapterLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'exercice_points',
        'lesson_points',
        'bonus',
    ];

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
}
