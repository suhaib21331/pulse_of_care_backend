<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\ServiceRequest;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Throwable;

class BookingController
{
    public function __construct(private BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function createBooking(ServiceRequest $request): JsonResponse
    {
        try {
            $service = $this->bookingService->createBooking($request->validated());

            return response()->json([
                'message' => 'Service booking created successfully.',
                'service' => $service,
                'status_code' => 201,
            ], 201);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to create service booking at this time.',
                'service' => null,
                'status_code' => 500,
            ], 500);
        }
    }
}
