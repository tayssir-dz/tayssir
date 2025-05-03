<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('default.app_version', '1.0.0');
        $this->migrator->add('default.resumes_active', false);
        $this->migrator->add('default.bac_solutions_active', false);
        $this->migrator->add('default.cards_tools_active', false);
    }
};
