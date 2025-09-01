<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Instagram
        $this->migrator->add('platform.instagram_url', 'https://www.instagram.com/tayssir.bac');
        $this->migrator->add('platform.instagram_active', false);

        // Facebook
        $this->migrator->add('platform.facebook_url', 'https://www.facebook.com/tayssir.bac');
        $this->migrator->add('platform.facebook_active', false);

        // TikTok
        $this->migrator->add('platform.tiktok_url', 'https://www.tiktok.com/@tayssir.bac');
        $this->migrator->add('platform.tiktok_active', false);

        // YouTube
        $this->migrator->add('platform.youtube_url', 'https://www.youtube.com/@tayssir.bac');
        $this->migrator->add('platform.youtube_active', false);

        // LinkedIn
        $this->migrator->add('platform.linkedin_url', 'https://www.linkedin.com/company/tayssir-bac');
        $this->migrator->add('platform.linkedin_active', false);
    }
};
