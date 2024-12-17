<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kossa\AlgerianCities\Commune;
use Kossa\AlgerianCities\Wilaya;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Storage;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements MustVerifyEmail, HasMedia, HasAvatar, FilamentUser
{
    use InteractsWithMedia;
    use HasFactory, Notifiable, HasApiTokens;
    use HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
        // return !$this->hasAnyRole(["student"]);
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
        'phone_number',
        'avatar_url',
        'email',
        'password',
        'wilaya_id',
        'commune_id',
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
    public function subscriptionCards()
    {
        return $this->hasMany(SubscriptionCard::class);
    }

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function subscription_cards()
    {
        return $this->hasMany(SubscriptionCard::class)
            ->where('redeemed_at', '!=', null)
            ->whereHas('subscription', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('ending_date')
                        ->orWhere('ending_date', '>', now());
                });
            })
            ->with('subscription')
            ->latest('redeemed_at');
    }

    public function getSubscriptionsAttribute()
    {
        $subscriptions = $this->subscription_cards
            ->map(fn($card) => $card->subscription)
            ->filter();

        $guestSubscription = Subscription::find(Subscription::GUEST_ID);
        if ($guestSubscription && !$subscriptions->contains('id', Subscription::GUEST_ID)) {
            $subscriptions->push($guestSubscription);
        }

        return $subscriptions->unique('id')->values();
    }

    public function getActiveSubscriptionsAttribute()
    {
        $subscriptions = $this->subscription_cards
            ->map(fn($card) => $card->subscription)
            ->filter();

        if ($subscriptions->isEmpty()) {
            // If no subscriptions, return guest only
            return collect([Subscription::find(Subscription::GUEST_ID)])
                ->filter();
        }

        // If has subscriptions, return them without guest
        return $subscriptions->unique('id')->values();
    }

    public function accessibleUnits()
    {
        return Unit::whereHas('subscriptions', function ($query) {
            $query->where('subscriptions.id', $this->subscription->subscription_id);
        });
    }
}


