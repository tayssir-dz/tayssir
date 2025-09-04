<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $app_version;

    public bool $resumes_active;

    public bool $bac_solutions_active;

    public bool $cards_tools_active;

    public string $payment_name;

    public string $payment_number;

    public static function group(): string
    {
        return 'default';
    }
}
