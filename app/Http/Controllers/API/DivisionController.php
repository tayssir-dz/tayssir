<?php

namespace App\Http\Controllers\API;

use App\Models\Division;
use App\Models\Subscription;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;

class DivisionController extends BaseController
{
    /**
     * Display a listing of the divisions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $divisions = Division::all();
        return $this->sendResponse($divisions, __("response.divisions_retrieved_successfully"));
    }

    /**
     * Display the specified division.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $division = Division::find($id);

        if (is_null($division)) {
            return $this->sendError(__("response.division_not_found"));
        }

        return $this->sendResponse($division, __("response.division_retrieved_successfully"));
    }
}
