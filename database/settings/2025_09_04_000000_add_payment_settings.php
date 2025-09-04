<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('default.payment_name', 'Tayssir Payment');
        $this->migrator->add('default.payment_number', '00799999002800000000');
    }
};
