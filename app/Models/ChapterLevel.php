<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getRecordTitleAttribute()
    {
        return $this->name;
    }
}
