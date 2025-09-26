<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Observers\UserObserver;
use App\Settings\PlatformSettings;
use App\Traits\User\HasProgress;
use App\Traits\User\HasSubscriptions;
use App\Traits\User\HasWilayaAndCommune;
use App\Traits\User\InteractsWithContent;
use App\Traits\User\IsPanelUser;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use InteractsWithMedia;
    use HasWilayaAndCommune;
    use HasSubscriptions;
    use InteractsWithContent;
    use HasProgress;
    use IsPanelUser;

    protected $fillable = [
        'name',
        'age',
        'phone_number',
        'avatar_url',
        'email',
        'google_id',
        'new_email',
        'password',
        'wilaya_id',
        'commune_id',
        'division_id',
        'email_verified_at',
        'referral_source_id',
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

    /**
     * The referral source (if any) associated with the user.
     */
    public function referralSource()
    {
        return $this->belongsTo(ReferralSource::class);
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

    public function manualPayments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getRecordTitleAttribute()
    {
        return $this->name . ' (' . $this->email . ')';
    }

    public function getProgressPercentageAttribute(): float
    {
        $leaderboard = $this->leaderboard;
        if (! $leaderboard || ! $leaderboard->max_points) {
            return 0.0;
        }

        return ($leaderboard->points / $leaderboard->max_points) * 100;
    }

    /**
     * Route mail notifications. Use the pending new email for the change email verification notification.
     */
    public function routeNotificationForMail($notification)
    {
        if ($this->new_email && $notification instanceof \App\Notifications\ChangeEmailVerificationNotification) {
            return $this->new_email;
        }

        return $this->email;
    }


    // scope admins, with role super_admin
    public function scopeAdmins($query)
    {
        return $query->role(['super_admin']);
    }

    public function scopeGoogleUsers($query)
    {
        return $query->whereNotNull('google_id');
    }

    public function scopeWithinPlatformPeriod(Builder $query): Builder
    {
        $settings = app(PlatformSettings::class);
        $from = $settings->platform_active_from ? Carbon::parse($settings->platform_active_from) : null;
        $to   = $settings->platform_active_to ? Carbon::parse($settings->platform_active_to) : null;

        if ($from && $to) {
            return $query->whereBetween('created_at', [$from, $to]);
        }

        if ($from) {
            return $query->where('created_at', '>=', $from);
        }

        if ($to) {
            return $query->where('created_at', '<=', $to);
        }
        return $query;
    }

    public function getAvatarImageAttribute(): ?string
    {
        $mediaUrl = $this->getFirstMediaUrl('avatar');
        if (!empty($mediaUrl)) {
            return $mediaUrl;
        }

        if ($this->avatar_url) {
            if (filter_var($this->avatar_url, FILTER_VALIDATE_URL)) {
                return $this->avatar_url;
            }

            $base = rtrim(config('app.url'), '/');
            $path = ltrim($this->avatar_url, '/');

            if (str_starts_with($path, 'storage/')) {
                return $base . '/' . $path;
            }

            return $base . '/storage/' . $path;
        }

        return null;
    }
}
