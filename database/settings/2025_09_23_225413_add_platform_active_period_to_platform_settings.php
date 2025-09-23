<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('platform.platform_active_from', null);
        $this->migrator->add('platform.platform_active_to', null);
    }
};
