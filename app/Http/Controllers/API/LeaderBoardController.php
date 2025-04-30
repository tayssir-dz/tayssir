<?php

namespace App\Http\Controllers\API;

use App\Models\LeaderBoard;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderBoardController extends BaseController
{
    // create function index that returns the leader board, paginated mapped with (id, user.name)
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $currentPage = $request->input('page', 1);

        $leaderBoard = LeaderBoard::with(['user' => function ($query) {
            $query->with(['wilaya', 'commune']);
        }])
            ->orderBy('points', 'desc')
            ->paginate($perPage, page: $currentPage);

        $leaderboardData = [];
        foreach ($leaderBoard as $item) {
            $leaderboardData[] = [
                'id' => $item->user->id,
                'name' => $item->user->name,
                'avatar_url' => config('app.url') . "/storage/" . $item->user->avatar_url,
                'points' => $item->points,
                'wilaya' => $item->user->wilaya ? $item->user->wilaya->arabic_name : null,
                'commune' => $item->user->commune ? $item->user->commune->arabic_name : null,
            ];
        }
        // Extract pagination metadata
        $result = [
            'page' => $currentPage,
            'per_page' => $perPage,
            'total_items' => LeaderBoard::count(), // Get total items
            'total_pages' => $leaderBoard->lastPage(),
            'current_page' => $currentPage,
            'data' => $leaderboardData
        ];

        // Return the data and the pagination metadata
        return $this->sendResponse($result);
    }
}
