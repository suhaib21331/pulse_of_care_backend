<?php

namespace App\Http\Controllers\api\v1;

use App\Services\ProviderOrderService;
use Illuminate\Http\JsonResponse;
use Throwable;

class ProviderOrderController
{
    public function __construct(private ProviderOrderService $providerOrderService) {}

    public function index(): JsonResponse
    {
        try {
            $result = $this->providerOrderService->getPendingOrders();

            if ($result['status_code'] !== 200) {
                return response()->json([
                    'message' => $result['message'],
                ], $result['status_code']);
            }

            return response()->json([
                'message' => 'Pending orders retrieved successfully.',
                'count' => $result['count'],
                'orders' => $result['orders'],
            ], 200);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to retrieve orders at this time.',
            ], 500);
        }
    }

    public function accept(int $assignment): JsonResponse
    {
        try {
            $result = $this->providerOrderService->acceptOrder($assignment);

            if ($result['status_code'] !== 200) {
                return response()->json([
                    'message' => $result['message'],
                ], $result['status_code']);
            }

          return response()->json([
    'message' => $result['message'],
    'order' => $result['order'],
    'provider' => $result['provider'],
], 200);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to accept order at this time.',
            ], 500);
        }
    }

    public function reject(int $assignment): JsonResponse
    {
        try {
            $result = $this->providerOrderService->rejectOrder($assignment);

            if ($result['status_code'] !== 200) {
                return response()->json([
                    'message' => $result['message'],
                ], $result['status_code']);
            }

            return response()->json([
                'message' => $result['message'],
            ], 200);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to reject order at this time.',
            ], 500);
        }
    }
}
