<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use BezhanSalleh\FilamentShield\Traits\HasPanelShield;

use App\Traits\HasWilayaAndCommune;
use App\Traits\HasProgress;
use App\Traits\HasSubscriptions;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use App\Observers\UserObserver;
use App\Traits\InteractsWithContent;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;


#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements MustVerifyEmail, HasMedia, HasAvatar, FilamentUser
{
    use HasFactory, Notifiable, HasApiTokens;
    use InteractsWithMedia;
    use HasRoles;
    use HasWilayaAndCommune;
    use HasProgress;
    use HasSubscriptions;
    use InteractsWithContent;

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'division_id', // Add this
    ];
    // protected $with = ['subscriptionCard'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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
}
