<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\UpdateAvailabilityRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Services\ProviderLocationService;
use Illuminate\Http\JsonResponse;
use Throwable;

class ProviderLocationController
{
    public function __construct(private ProviderLocationService $providerLocationService) {}

    public function updateLocation(UpdateLocationRequest $request): JsonResponse
    {
        try {
            $result = $this->providerLocationService->updateLocation(
                (float) $request->validated('latitude'),
                (float) $request->validated('longitude'),
            );

            return response()->json([
                'message' => $result['message'],
                'status_code' => $result['status_code'],
            ], $result['status_code']);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to update location at this time.',
                'status_code' => 500,
            ], 500);
        }
    }

    public function updateAvailability(UpdateAvailabilityRequest $request): JsonResponse
    {
        try {
            $result = $this->providerLocationService->updateAvailability(
                (bool) $request->validated('is_available'),
            );

            return response()->json([
                'message' => $result['message'],
                'status_code' => $result['status_code'],
            ], $result['status_code']);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to update availability at this time.',
                'status_code' => 500,
            ], 500);
        }
    }
}
