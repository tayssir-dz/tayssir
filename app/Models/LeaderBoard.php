<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderBoard extends Model
{
    protected $fillable = ['user_id', 'points', 'max_points', 'last_updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
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
