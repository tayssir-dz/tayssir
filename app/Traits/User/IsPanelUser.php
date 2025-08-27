<?php

namespace App\Traits\User;

use Filament\Panel;
use Illuminate\Support\Facades\Storage;

trait IsPanelUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('super_admin');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }
}
