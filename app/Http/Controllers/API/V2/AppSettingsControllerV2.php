<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\API\BaseController;
use App\Settings\AppSettings;
use Dedoc\Scramble\Attributes\Group;

use Illuminate\Http\Request;

#[Group('App Settings APIs', weight: 4)]
class AppSettingsControllerV2 extends BaseController
{
    /**
     * Get app settings.
     *
     * This endpoint returns an object containing app settings.
     */
    public function index()
    {
        return $this->sendResponse([
            'app_version' => app(AppSettings::class)->app_version,
            'resumes_active' => app(AppSettings::class)->resumes_active,
            'bac_solutions_active' => app(AppSettings::class)->bac_solutions_active,
            'cards_tools_active' => app(AppSettings::class)->cards_tools_active,
            'payment_name' => app(AppSettings::class)->payment_name,
            'payment_number' => app(AppSettings::class)->payment_number,
            'payment_active' => app(AppSettings::class)->payment_active,
            'chargily_payment_active' => app(AppSettings::class)->chargily_payment_active,
        ]);
    }

    /**
     * Get app settings (structured).
     *
     * This endpoint returns an object containing app settings, same content as the the previous endpoint, but app variables and payment variables are separated.
     */
    public function separated()
    {
        return $this->sendResponse([
            'app' => [
                'app_version' => app(AppSettings::class)->app_version,
                'resumes_active' => app(AppSettings::class)->resumes_active,
                'bac_solutions_active' => app(AppSettings::class)->bac_solutions_active,
                'cards_tools_active' => app(AppSettings::class)->cards_tools_active,
            ],
            'payment' => [
                'payment_name' => app(AppSettings::class)->payment_name,
                'payment_number' => app(AppSettings::class)->payment_number,
                'payment_active' => app(AppSettings::class)->payment_active,
                'chargily_payment_active' => app(AppSettings::class)->chargily_payment_active,
            ],
        ]);
    }
}
