<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\NotificationResource;
use App\Models\Promoter;
use Illuminate\Http\Request;
use Dedoc\Scramble\Attributes\Group;

#[Group('Notifications APIs', weight: 3)]
class NotificationControllerV2 extends BaseController
{
    /**
     * Get All Notifications.
     *
     * This endpoint returns all user notifications
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'nullable|sometimes|integer|min:1|max:100',
            'page' => 'nullable|sometimes|integer|min:1',
        ]);

        $user = $request->user();

        $perPage = (int) ($validated['per_page'] ?? 10);
        $page = (int) ($validated['page'] ?? 1);

        $paginator = $user->notifications()
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);


        $notifications = NotificationResource::collection($paginator->getCollection());


        $unreadCount = $user->unreadNotifications()->count();

        return $this->sendResponse([
            'has_notifications' => $paginator->total() > 0,
            'has_unread' => $unreadCount > 0,
            'unread_count' => $unreadCount,
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
            'notifications' => $notifications,
        ]);
    }


    /**
     * Mark Notifications as Read.
     *
     * This endpoint marks one or many notifications as read for the authenticated user.
     */
    public function markAsRead(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'uuid|exists:notifications,id',
        ]);

        $user = $request->user();

        $user->notifications()
            ->whereIn('id', $validated['ids'])
            ->update(['read_at' => now()]);


        return $this->sendResponse(["a" => "b"]);
        // return response()->json([
        //     'message' => 'Notifications marked as read successfully.',
        // ]);
    }


    /**
     * Mark Notifications as Unread.
     *
     * This endpoint marks one or many notifications as unread for the authenticated user.
     */
    public function markAsUnread(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'uuid|exists:notifications,id',
        ]);

        $user = $request->user();

        $user->notifications()
            ->whereIn('id', $validated['ids'])
            ->update(['read_at' => null]);

        return $this->sendResponse();
    }

    /**
     * Delete Notifications.
     *
     * This endpoint deletes one or many notifications for the authenticated user.
     */
    public function delete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'uuid|exists:notifications,id',
        ]);

        $user = $request->user();

        // Ensure only user's own notifications are deleted
        $user->notifications()
            ->whereIn('id', $validated['ids'])
            ->delete();

        return $this->sendResponse();
    }
}
