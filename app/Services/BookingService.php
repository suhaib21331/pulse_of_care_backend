<?php

namespace App\Services;

use App\Models\CompanionService;
use App\Models\DriverService;
use App\Models\NurseService;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class BookingService
{
    private $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function createBooking($request): Service
    {
        $elder = auth()->guard('api')->user();

        $service = DB::transaction(function () use ($request, $elder): Service {
            $service = Service::create([
                'elder_id' => $elder->id,
                'service_type' => $request['service_type'],
                'service_condition' => $request['service_condition'],
                'service_address' => $request['service_type'] === 'driver' ? null : ($request['service_address'] ?? null),
                'service_latitude' => $request['service_type'] === 'driver' ? null : ($request['service_latitude'] ?? null),
                'service_longitude' => $request['service_type'] === 'driver' ? null : ($request['service_longitude'] ?? null),
                'status' => 'pending',
            ]);

            $this->createTypeSpecificService($service->id, $request);

            $service->load([
                'nurseService',
                'driverService',
                'companionService',
            ]);

            $assignmentsCreated = $this->matchingService->createAssignmentsForService($service);

            if ($assignmentsCreated > 0) {
                $service->update(['status' => 'assigned']);
            }

            return $service;
        });

        return $service->load([
            'nurseService',
            'driverService',
            'companionService',
            'serviceAssignments',
        ]);
    }

    private function createTypeSpecificService($serviceId, $request): void
    {
        if ($request['service_type'] === 'nurse') {
            NurseService::create([
                'service_id' => $serviceId,
                'nurse_major' => $request['nurse_major'],
            ]);
        }

        if ($request['service_type'] === 'driver') {
            DriverService::create([
                'service_id' => $serviceId,
                'pickup_address' => $request['pickup_address'],
                'pickup_latitude' => $request['pickup_latitude'],
                'pickup_longitude' => $request['pickup_longitude'],
                'dropoff_address' => $request['dropoff_address'],
                'dropoff_latitude' => $request['dropoff_latitude'],
                'dropoff_longitude' => $request['dropoff_longitude'],
            ]);
        }

        if ($request['service_type'] === 'companion') {
            CompanionService::create([
                'service_id' => $serviceId,
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time'],
                'period' => $request['period'],
            ]);
        }
    }
}
