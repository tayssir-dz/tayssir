<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Settings\AppSettings;
use Dedoc\Scramble\Attributes\Group;

#[Group('App Settings APIs', weight: 8)]
class AppSettingsController extends BaseController
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
        ]);
    }
}
