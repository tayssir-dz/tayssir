<?php

namespace App\Http\Controllers\API;

use App\Models\Division;
use Illuminate\Http\Request;

class ContentController
{
    public function getUserContent(Request $request)
    {

        $userSubscriptions = $request->user()->subscriptions->pluck('id');
        $division = Division::where('id', 2)->first();

        return $division->load([
            'materials.units' => function ($query) use ($userSubscriptions) {
                $query->whereHas('subscriptions', function ($q) use ($userSubscriptions) {
                    $q->whereIn('subscriptions.id', $userSubscriptions);
                })->with([
                            'chapters' => function ($q) use ($userSubscriptions) {
                                $q->whereHas('subscriptions', function ($sub) use ($userSubscriptions) {
                                    $sub->whereIn('subscriptions.id', $userSubscriptions);
                                })->with('questions');
                            }
                        ]);
            }
        ]);
    }
}
