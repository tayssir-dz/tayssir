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
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function getIsReadAttribute(): bool
    {
        return (bool) $this->read_at;
    }

    public function markAsRead(): void
    {
        $this->read_at = now();
        $this->save();
    }

    public function markAsUnread(): void
    {
        $this->read_at = null;
        $this->save();
    }
}
