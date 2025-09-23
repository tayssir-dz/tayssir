<?php

namespace App\Http\Controllers;

use App\Settings\PlatformSettings;
use Illuminate\Http\RedirectResponse;

class SocialRedirectController
{
    public function __invoke(string $platform, PlatformSettings $settings): RedirectResponse
    {
        $platforms = [
            'instagram' => [
                'url' => $settings->instagram_url ?? null,
                'active' => (bool) ($settings->instagram_active ?? false),
            ],
            'facebook' => [
                'url' => $settings->facebook_url ?? null,
                'active' => (bool) ($settings->facebook_active ?? false),
            ],
            'tiktok' => [
                'url' => $settings->tiktok_url ?? null,
                'active' => (bool) ($settings->tiktok_active ?? false),
            ],
            'youtube' => [
                'url' => $settings->youtube_url ?? null,
                'active' => (bool) ($settings->youtube_active ?? false),
            ],
            'linkedin' => [
                'url' => $settings->linkedin_url ?? null,
                'active' => (bool) ($settings->linkedin_active ?? false),
            ],
        ];

        if (! isset($platforms[$platform])) {
            abort(404);
        }

        $data = $platforms[$platform];

        if (! $data['active'] || empty($data['url'])) {
            abort(404);
        }

        return redirect()->away($data['url']);
    }
}
