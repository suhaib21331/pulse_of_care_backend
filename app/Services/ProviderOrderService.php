<?php

namespace App\Services;

use App\Events\CustomerOrderAccepted;
use App\Models\Companion;
use App\Models\Driver;
use App\Models\Nurse;
use App\Models\Service;
use App\Models\ServiceAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProviderOrderService
{
    public function __construct(private NotificationService $notificationService) {}

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
            'orders' => $assignments
                ->map(fn (ServiceAssignment $assignment) => $this->formatAssignment($assignment))
                ->values(),
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

            if ($service === null) {
                return [
                    'status_code' => 404,
                    'message' => 'Service not found.',
                ];
            }

            if ($service->status === 'accepted') {
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

            $service->update([
                'status' => 'accepted',
            ]);

            $assignment->load([
                'service.nurseService',
                'service.driverService',
                'service.companionService',
                'service.elder',
            ]);

            $this->dispatchCustomerAcceptedNotification($service, $assignment, $provider, $user);

            return [
                'status_code' => 200,
                'message' => 'Order accepted successfully.',
                'order' => $this->formatAssignment($assignment),
                'provider' => $this->formatProvider($provider, $user),
            ];
        });
    }

    public function getAcceptedOrder(): array
    {
        $user = auth()->guard('api')->user();
        $provider = $this->resolveProviderProfile($user);

        if ($provider === null) {
            return [
                'status_code' => 403,
                'message' => 'Provider profile not found.',
            ];
        }

        $assignment = ServiceAssignment::with([
            'service.nurseService',
            'service.driverService',
            'service.companionService',
            'service.elder',
        ])
            ->where('provider_id', $provider->id)
            ->where('provider_type', $user->account_type)
            ->where('status', 'accepted')
            ->latest()
            ->first();

        if ($assignment === null) {
            return [
                'status_code' => 404,
                'message' => 'No accepted order found.',
            ];
        }

        return [
            'status_code' => 200,
            'order' => $this->formatAssignment($assignment),
            'provider' => $this->formatProvider($provider, $user),
        ];
    }

    public function markArrived(string $serviceId): array
    {
        $user = auth()->guard('api')->user();
        $provider = $this->resolveProviderProfile($user);

        if ($provider === null) {
            return [
                'status_code' => 403,
                'message' => 'Provider profile not found.',
            ];
        }

        $assignment = ServiceAssignment::where('service_id', $serviceId)
            ->where('provider_id', $provider->id)
            ->where('provider_type', $user->account_type)
            ->where('status', 'accepted')
            ->first();

        if ($assignment === null) {
            return [
                'status_code' => 404,
                'message' => 'No accepted assignment found for this service.',
            ];
        }

        $service = Service::find($serviceId);

        if ($service === null || $service->status !== 'accepted') {
            return [
                'status_code' => 409,
                'message' => 'Service is not in an accepted state.',
            ];
        }

        $service->update(['status' => 'in_progress']);

        return [
            'status_code' => 200,
            'message' => 'Marked as arrived. Service is now in progress.',
        ];
    }

    public function completeService(string $serviceId): array
    {
        $user = auth()->guard('api')->user();
        $provider = $this->resolveProviderProfile($user);

        if ($provider === null) {
            return [
                'status_code' => 403,
                'message' => 'Provider profile not found.',
            ];
        }

        $assignment = ServiceAssignment::where('service_id', $serviceId)
            ->where('provider_id', $provider->id)
            ->where('provider_type', $user->account_type)
            ->where('status', 'accepted')
            ->first();

        if ($assignment === null) {
            return [
                'status_code' => 404,
                'message' => 'No accepted assignment found for this service.',
            ];
        }

        $service = Service::find($serviceId);

        if ($service === null || $service->status !== 'in_progress') {
            return [
                'status_code' => 409,
                'message' => 'Service must be in progress before it can be completed.',
            ];
        }

        $service->update(['status' => 'completed']);

        return [
            'status_code' => 200,
            'message' => 'Service completed successfully.',
        ];
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

    private function dispatchCustomerAcceptedNotification(
        Service $service,
        ServiceAssignment $assignment,
        mixed $provider,
        mixed $providerUser
    ): void {
        $elderUserId = $service->elder_id;
        $elder = User::find($elderUserId);

        if ($elder === null) {
            return;
        }

        $payload = [
            'type' => 'customer_order_accepted',
            'message' => 'A '.$providerUser->account_type.' accepted your order.',
            'service_id' => $service->id,
            'assignment_id' => $assignment->id,
            'provider' => [
                'type' => $providerUser->account_type,
                'name' => $providerUser->full_name,
                'phone' => $providerUser->phone_number,
                'distance_km' => $assignment->distance_km,
            ],
        ];

        $this->notificationService->create($elderUserId, 'customer_order_accepted', $payload);

        try {
            broadcast(new CustomerOrderAccepted(
                elderUserId: $elderUserId,
                serviceId: $service->id,
                assignmentId: $assignment->id,
                providerType: $providerUser->account_type,
                providerName: $providerUser->full_name,
                providerPhone: $providerUser->phone_number,
                distanceKm: (float) $assignment->distance_km,
            ));
        } catch (\Throwable $e) {
            report($e);
        }
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
                'id' => $service?->id,
                'service_type' => $service?->service_type,
                'service_condition' => $service?->service_condition,
                'service_address' => $service?->service_address,
                'service_latitude' => $service?->service_latitude,
                'service_longitude' => $service?->service_longitude,
                'status' => $service?->status,

                'elder' => [
                    'id' => $service?->elder?->id,
                    'name' => $service?->elder?->full_name,
                    'phone' => $service?->elder?->phone_number,
                ],

                'details' => $service?->nurseService
                    ?? $service?->driverService
                    ?? $service?->companionService,
            ],
        ];
    }

    private function formatProvider(mixed $provider, mixed $user): array
    {
        return [
            'id' => $provider->id,
            'user_id' => $user->id,
            'name' => $user->full_name,
            'phone' => $user->phone_number,
            'type' => $user->account_type,
        ];
    }
}
