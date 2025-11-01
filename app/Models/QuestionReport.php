<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionReport extends Model
{
    protected $fillable = [
        'user_id',
        'question_id',
        'description',
        'is_solved',
        'is_contacted',
    ];

    protected $casts = [
        'is_solved' => 'boolean',
        'is_contacted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
