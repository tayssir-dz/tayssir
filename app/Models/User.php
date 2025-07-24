<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use BezhanSalleh\FilamentShield\Traits\HasPanelShield;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

use App\Observers\UserObserver;

use Spatie\Permission\Traits\HasRoles;
use App\Traits\User\HasWilayaAndCommune;
use App\Traits\User\HasProgress;
use App\Traits\User\HasSubscriptions;
use App\Traits\User\InteractsWithContent;
use App\Traits\User\IsPanelUser;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements MustVerifyEmail, HasMedia, HasAvatar, FilamentUser
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use InteractsWithMedia;
    use HasRoles;
    use HasWilayaAndCommune;
    use HasProgress;
    use HasSubscriptions;
    use InteractsWithContent;
    use IsPanelUser;

    protected $fillable = [
        'name',
        "age",
        'phone_number',
        'avatar_url',
        'email',
        'new_email',
        'password',
        'wilaya_id',
        'commune_id',
        'division_id',
    ];
    // protected $with = ['subscriptionCard'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
    public function answers()
    {
        return $this->hasMany(UserAnswer::class);
    }
    public function chapterBonuses()
    {
        return $this->hasMany(UserChapterBonus::class);
    }

    public function leaderboard()
    {
        return $this->hasOne(LeaderBoard::class);
    }

    public function getRecordTitleAttribute()
    {
        return $this->name . ' (' . $this->email . ')';
    }


    public function getProgressPercentageAttribute(): float
    {
        $leaderboard = $this->leaderboard;
        if (!$leaderboard || !$leaderboard->max_points) {
            return 0.0;
        }

        return ($leaderboard->points / $leaderboard->max_points) * 100;
    }
}
