<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LeaderBoard extends Model
{
    protected $fillable = ['user_id', 'points', 'max_points', 'last_updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope: filter leaderboard entries whose users fall within the platform period
    public function scopeWithinPlatformPeriod(Builder $query): Builder
    {
        return $query->whereHas('user', function (Builder $q) {
            $q->withinPlatformPeriod();
        });
    }

    public function getMaxAttribute(): int
    {
        // Return stored max_points instead of calculating every time
        return $this->max_points ?? 0;
    }

    /**
     * Calculate and update max points for this user
     */
    public function updateMaxPoints(): void
    {
        $this->max_points = $this->user->maxPoints();
        $this->save();
    }
}
