<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PlatformSettings extends Settings
{
    public ?string $platform_active_from;
    public ?string $platform_active_to;

    public string $instagram_url;
    public bool $instagram_active;

    public string $facebook_url;
    public bool $facebook_active;

    public string $tiktok_url;
    public bool $tiktok_active;

    public string $youtube_url;
    public bool $youtube_active;

    public string $linkedin_url;
    public bool $linkedin_active;

    public static function group(): string
    {
        return 'platform';
    }
}
