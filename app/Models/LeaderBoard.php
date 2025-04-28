<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderBoard extends Model
{
    protected $fillable = ['user_id', 'points', 'last_updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
