<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Models\ReferralSource;

class ReferralSourceController extends BaseController
{
    /**
     * Display a listing of referral sources.
     */
    public function index()
    {
        $sources = ReferralSource::query()
            ->select('id', 'name', 'created_at')
            ->withCount('users')
            ->orderBy('name')
            ->get()
            ->map(function ($source) {
                return [
                    'id' => $source->id,
                    'name' => $source->name,
                    'users_count' => $source->users_count,
                    'icon' => $source->icon,
                ];
            });

        return $this->sendResponse($sources, __('response.referral_sources_retrieved_successfully'));
    }
}
