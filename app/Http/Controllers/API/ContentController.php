<?php

namespace App\Http\Controllers\API;

use App\Models\Division;
use Illuminate\Http\Request;

class ContentController extends BaseController
{
    public function getUserContent(Request $request)
    {

        $user = $request->user();
        if (!$user->division) {
            return $this->sendError(__("response.an_error_occurred"));
        }
        $content = $user->content;

        return $this->sendResponse($content);
    }
}
