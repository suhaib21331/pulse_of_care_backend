<?php

namespace App\Services;

use App\Models\AppNotification;

class NotificationService
{
    public function create(string $userId, string $type, array $data): AppNotification
    {
        return AppNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'data' => $data,
        ]);
    }

    public function getPendingForUser(string $userId): array
    {
        $notifications = AppNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->latest()
            ->get();

        return [
            'status_code' => 200,
            'count' => $notifications->count(),
            'notifications' => $notifications->map(fn (AppNotification $n) => [
                'id' => $n->id,
                'type' => $n->type,
                'data' => $n->data,
                'created_at' => $n->created_at,
            ])->values(),
        ];
    }

    public function markAsRead(string $notificationId, string $userId): array
    {
        $notification = AppNotification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification === null) {
            return [
                'status_code' => 404,
                'message' => 'Notification not found.',
            ];
        }

        $notification->update(['read_at' => now()]);

        return [
            'status_code' => 200,
            'message' => 'Notification marked as read.',
        ];
    }
}
