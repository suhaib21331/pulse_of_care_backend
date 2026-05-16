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

    public function status(string $service): JsonResponse
    {
        try {
            $result = $this->bookingService->getBookingStatus($service);

            if ($result['status_code'] !== 200) {
                return response()->json([
                    'message' => $result['message'],
                ], $result['status_code']);
            }

            return response()->json([
                'message' => $result['message'],
                'service' => $result['service'],
                'assignments' => $result['assignments'],
                'accepted_provider' => $result['accepted_provider'],
            ], 200);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to retrieve booking status at this time.',
            ], 500);
        }
    }

    public function requests(): JsonResponse
    {
        try {
            $result = $this->bookingService->getCustomerRequests();

            return response()->json([
                'data' => $result['data'],
            ], 200);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to retrieve requests at this time.',
            ], 500);
        }
    }

    public function cancel(string $service): JsonResponse
    {
        try {
            $result = $this->bookingService->cancelRequest($service);

            return response()->json([
                'message' => $result['message'],
            ], $result['status_code']);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to cancel request at this time.',
            ], 500);
        }
    }
}
