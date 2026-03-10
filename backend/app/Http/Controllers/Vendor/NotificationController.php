<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return the latest 20 notifications for the authenticated vendor.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();

        $notifications = Notification::forUser($userId)
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'type'       => $n->type,
                    'title'      => $n->title,
                    'message'    => $n->message,
                    'data'       => $n->data,
                    'is_read'    => $n->is_read,
                    'created_at' => $n->created_at,
                ];
            });

        return response()->json(['data' => $notifications]);
    }

    /**
     * Return the count of unread notifications.
     */
    public function unreadCount(): JsonResponse
    {
        $count = Notification::forUser(auth()->id())->unread()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(int $id): JsonResponse
    {
        $notification = Notification::forUser(auth()->id())->findOrFail($id);
        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllRead(): JsonResponse
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
