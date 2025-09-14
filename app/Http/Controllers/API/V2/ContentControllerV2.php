<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\API\BaseController;
use App\Services\Content\ContentService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Content Management APIs V2', weight: 5)]
class ContentControllerV2 extends BaseController
{
    public function __construct(private ContentService $contentService) {}

    /**
     * Get user content
     * .
     * Includes: units outside subscription (premium) and chapters outside subscription within subscribed units (premium).
     */
    public function getUserContent(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->division) {
            return $this->sendError(__('response.an_error_occurred'));
        }

        $data = $this->contentService->getUserContent($user);

        return $this->sendResponse($data);
    }
}
