<?php

namespace App\Services;

use App\Models\Companion;
use App\Models\Driver;
use App\Models\Nurse;
use App\Models\Service;
use App\Models\ServiceAssignment;
use Illuminate\Support\Facades\DB;

class ProviderOrderService
{
    public function getPendingOrders(): array
    {
        $user = auth()->guard('api')->user();
        $provider = $this->resolveProviderProfile($user);

        if ($provider === null) {
            return [
                'status_code' => 403,
                'message' => 'Provider profile not found.',
            ];
        }

        $assignments = ServiceAssignment::with([
            'service.nurseService',
            'service.driverService',
            'service.companionService',
            'service.elder',
        ])
            ->where('provider_id', $provider->id)
            ->where('provider_type', $user->account_type)
            ->where('status', 'pending')
            ->orderByDesc('matching_score')
            ->get();

        return [
            'status_code' => 200,
            'count' => $assignments->count(),
            'orders' => $assignments->map(fn (ServiceAssignment $assignment) => $this->formatAssignment($assignment)),
        ];
    }

    public function acceptOrder(int $assignmentId): array
    {
        $user = auth()->guard('api')->user();
        $provider = $this->resolveProviderProfile($user);

        if ($provider === null) {
            return [
                'status_code' => 403,
                'message' => 'Provider profile not found.',
            ];
        }

        return DB::transaction(function () use ($assignmentId, $provider, $user): array {
            $assignment = ServiceAssignment::lockForUpdate()
                ->where('id', $assignmentId)
                ->where('provider_id', $provider->id)
                ->where('provider_type', $user->account_type)
                ->where('status', 'pending')
                ->first();

            if ($assignment === null) {
                return [
                    'status_code' => 404,
                    'message' => 'Assignment not found or no longer available.',
                ];
            }

            $service = Service::lockForUpdate()->find($assignment->service_id);

            if ($service === null || $service->status === 'accepted') {
                return [
                    'status_code' => 409,
                    'message' => 'This order has already been accepted by another provider.',
                ];
            }

            $assignment->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            ServiceAssignment::where('service_id', $assignment->service_id)
                ->where('id', '!=', $assignment->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'expired',
                    'responded_at' => now(),
                ]);

            $service->update(['status' => 'accepted']);

            return [
                'status_code' => 200,
                'message' => 'Order accepted successfully.',
                'order' => $this->formatAssignment($assignment->load([
                    'service.nurseService',
                    'service.driverService',
                    'service.companionService',
                    'service.elder',
                ])),
            ];
        });
    }

    public function rejectOrder(int $assignmentId): array
    {
        $user = auth()->guard('api')->user();
        $provider = $this->resolveProviderProfile($user);

        if ($provider === null) {
            return [
                'status_code' => 403,
                'message' => 'Provider profile not found.',
            ];
        }

        $assignment = ServiceAssignment::where('id', $assignmentId)
            ->where('provider_id', $provider->id)
            ->where('provider_type', $user->account_type)
            ->where('status', 'pending')
            ->first();

        if ($assignment === null) {
            return [
                'status_code' => 404,
                'message' => 'Assignment not found or no longer pending.',
            ];
        }

        $assignment->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);

        return [
            'status_code' => 200,
            'message' => 'Order rejected successfully.',
        ];
    }

    private function resolveProviderProfile(mixed $user): Nurse|Driver|Companion|null
    {
        return match ($user->account_type) {
            'nurse' => Nurse::where('user_id', $user->id)->first(),
            'driver' => Driver::where('user_id', $user->id)->first(),
            'companion' => Companion::where('user_id', $user->id)->first(),
            default => null,
        };
    }

    private function formatAssignment(ServiceAssignment $assignment): array
    {
        $service = $assignment->service;

        return [
            'assignment_id' => $assignment->id,
            'status' => $assignment->status,
            'distance_km' => $assignment->distance_km,
            'matching_score' => $assignment->matching_score,
            'responded_at' => $assignment->responded_at,
            'created_at' => $assignment->created_at,
            'service' => [
                'id' => $service->id,
                'service_type' => $service->service_type,
                'service_condition' => $service->service_condition,
                'service_address' => $service->service_address,
                'service_latitude' => $service->service_latitude,
                'service_longitude' => $service->service_longitude,
                'status' => $service->status,
                'details' => $service->nurseService
                    ?? $service->driverService
                    ?? $service->companionService,
                'elder' => [
                    'id' => $service->elder?->id,
                    'name' => $service->elder?->full_name,
                    'phone' => $service->elder?->phone_number,
                ],
            ],
        ];
    }
}
