<?php

namespace App\Http\Controllers\API;

use App\Settings\AppSettings;

class AppSettingsController extends BaseController
{
    public function index()
    {
        return $this->sendResponse([
            'app_version' => app(AppSettings::class)->app_version,
            'resumes_active' => app(AppSettings::class)->resumes_active,
            'bac_solutions_active' => app(AppSettings::class)->bac_solutions_active,
            'cards_tools_active' => app(AppSettings::class)->cards_tools_active,
        ]);

    }
}
