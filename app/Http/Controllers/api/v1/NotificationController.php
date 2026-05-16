<?php

namespace App\Http\Controllers\api\v1;

use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Throwable;

class NotificationController
{
    public function __construct(private NotificationService $notificationService) {}

    public function index(): JsonResponse
    {
        try {
            $userId = auth()->guard('api')->id();
            $result = $this->notificationService->getPendingForUser($userId);

            return response()->json([
                'message' => 'Notifications retrieved successfully.',
                'count' => $result['count'],
                'notifications' => $result['notifications'],
            ], 200);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to retrieve notifications at this time.',
            ], 500);
        }
    }

    public function markRead(string $notification): JsonResponse
    {
        try {
            $userId = auth()->guard('api')->id();
            $result = $this->notificationService->markAsRead($notification, $userId);

            return response()->json([
                'message' => $result['message'],
            ], $result['status_code']);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to mark notification as read.',
            ], 500);
        }
    }
}
