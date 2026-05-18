<?php

namespace App\Services;

use App\Models\Companion;
use App\Models\CompanionService;
use App\Models\Driver;
use App\Models\DriverService;
use App\Models\Nurse;
use App\Models\NurseService;
use App\Models\Service;
use App\Models\ServiceAssignment;
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

    public function getBookingStatus(string $serviceId): array
    {
        $user = auth()->guard('api')->user();

        $service = Service::with([
            'nurseService',
            'driverService',
            'companionService',
            'serviceAssignments',
        ])
            ->where('id', $serviceId)
            ->where('elder_id', $user->id)
            ->first();

        if ($service === null) {
            return [
                'status_code' => 404,
                'message' => 'Booking not found.',
            ];
        }

        $assignments = $service->serviceAssignments;

        $counts = [
            'pending_count' => $assignments->where('status', 'pending')->count(),
            'rejected_count' => $assignments->where('status', 'rejected')->count(),
            'expired_count' => $assignments->where('status', 'expired')->count(),
            'accepted_count' => $assignments->where('status', 'accepted')->count(),
        ];

        $acceptedAssignment = $assignments->firstWhere('status', 'accepted');
        $acceptedProvider = null;

        if ($acceptedAssignment !== null) {
            $acceptedProvider = $this->resolveAcceptedProvider($acceptedAssignment);
        }

        return [
            'status_code' => 200,
            'message' => 'Booking status retrieved successfully.',
            'service' => [
                'id' => $service->id,
                'status' => $service->status,
                'service_type' => $service->service_type,
                'service_condition' => $service->service_condition,
                'service_address' => $service->service_address,
                'service_latitude' => $service->service_latitude,
                'service_longitude' => $service->service_longitude,
                'details' => $service->nurseService
                    ?? $service->driverService
                    ?? $service->companionService,
            ],
            'assignments' => $counts,
            'accepted_provider' => $acceptedProvider,
        ];
    }

    public function getCustomerRequests(): array
    {
        $user = auth()->guard('api')->user();

        $services = Service::with([
            'nurseService',
            'driverService',
            'companionService',
            'acceptedAssignment',
        ])
            ->where('elder_id', $user->id)
            ->latest('created_at')
            ->get();

        return [
            'status_code' => 200,
            'data' => $services->map(fn (Service $service) => $this->formatRequest($service))->values(),
        ];
    }

    private function formatRequest(Service $service): array
    {
        $accepted = $service->acceptedAssignment;
        $providerName = null;
        $providerPhone = null;

        if ($accepted !== null) {
            $provider = match ($accepted->provider_type) {
                'nurse' => Nurse::with('user')->find($accepted->provider_id),
                'driver' => Driver::with('user')->find($accepted->provider_id),
                'companion' => Companion::with('user')->find($accepted->provider_id),
                default => null,
            };

            $providerName = $provider?->user?->full_name;
            $providerPhone = $provider?->user?->phone_number;
        }

        [$date, $time] = $this->resolveSchedule($service);

        return [
            'id' => $service->id,
            'service_type' => $service->service_type,
            'service_condition' => $service->service_condition,
            'status' => (string) $service->status,
            'address' => $service->service_type === 'driver'
                ? ($service->driverService?->pickup_address ?? '')
                : ($service->service_address ?? ''),
            'date' => $date,
            'time' => $time,
            'notes' => '',
            'provider_name' => $providerName,
            'provider_phone' => $providerPhone,
            'details' => $service->nurseService
                ?? $service->driverService
                ?? $service->companionService,
        ];
    }

    private function resolveSchedule(Service $service): array
    {
        if ($service->service_type === 'nurse' && $service->nurseService !== null) {
            return [
                $service->nurseService->scheduled_date?->format('Y-m-d'),
                $service->nurseService->scheduled_time,
            ];
        }

        if ($service->service_type === 'companion' && $service->companionService !== null) {
            return [
                $service->companionService->scheduled_date?->format('Y-m-d'),
                $service->companionService->scheduled_time,
            ];
        }

        return [null, null];
    }

    public function cancelRequest(string $serviceId): array
    {
        $user = auth()->guard('api')->user();

        $service = Service::where('id', $serviceId)
            ->where('elder_id', $user->id)
            ->first();

        if ($service === null) {
            return [
                'status_code' => 404,
                'message' => 'Request not found.',
            ];
        }

        if (in_array($service->status, ['in_progress', 'completed', 'cancelled'], true)) {
            return [
                'status_code' => 409,
                'message' => 'This request cannot be cancelled.',
            ];
        }

        DB::transaction(function () use ($service): void {
            ServiceAssignment::where('service_id', $service->id)
                ->whereIn('status', ['pending', 'accepted'])
                ->update(['status' => 'cancelled', 'responded_at' => now()]);

            $service->update(['status' => 'cancelled']);
        });

        return [
            'status_code' => 200,
            'message' => 'Service cancelled successfully.',
            'service' => [
                'id' => $service->id,
                'status' => 'cancelled',
            ],
        ];
    }

    private function resolveAcceptedProvider(ServiceAssignment $assignment): array
    {
        $provider = match ($assignment->provider_type) {
            'nurse' => Nurse::with('user')->find($assignment->provider_id),
            'driver' => Driver::with('user')->find($assignment->provider_id),
            'companion' => Companion::with('user')->find($assignment->provider_id),
            default => null,
        };

        if ($provider === null) {
            return [];
        }

        $user = $provider->user;

        $profile = $provider->toArray();
        unset($profile['user']);

        return [
            'assignment_id' => $assignment->id,
            'provider_id' => $provider->id,
            'type' => $assignment->provider_type,
            'name' => $user?->full_name,
            'phone' => $user?->phone_number,
            'distance_km' => $assignment->distance_km,
            'matching_score' => $assignment->matching_score,
            'accepted_at' => $assignment->responded_at,
            'profile' => $profile,
        ];
    }

    private function createTypeSpecificService($serviceId, $request): void
    {
        if ($request['service_type'] === 'nurse') {
            NurseService::create([
                'service_id' => $serviceId,
                'nurse_major' => $request['nurse_major'],
                'scheduled_date' => $request['scheduled_date'] ?? null,
                'scheduled_time' => $request['scheduled_time'] ?? null,
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
                'scheduled_date' => $request['scheduled_date'] ?? null,
                'scheduled_time' => $request['scheduled_time'] ?? null,
            ]);
        }
    }
}
