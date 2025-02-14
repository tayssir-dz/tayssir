<?php

namespace App\Http\Controllers\API;

use App\Models\Division;
use Illuminate\Http\Request;

class ContentController extends BaseController
{
    public function getUserContent(Request $request)
    {

        // $userSubscriptions = $request->user()->subscriptions->pluck('id');
        $content = $request->user()->content;

        return $this->sendResponse($content);
    }
}
