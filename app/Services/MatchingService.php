<?php

namespace App\Services;

use App\Models\Companion;
use App\Models\Driver;
use App\Models\Nurse;
use App\Models\ProviderLocation;
use App\Models\Service;
use App\Models\ServiceAssignment;
use Illuminate\Database\Eloquent\Builder;

class MatchingService
{
    public function createAssignmentsForService(Service $service): int
    {
        [$latitude, $longitude] = $this->resolveMatchingCoordinates($service);

        if ($latitude === null || $longitude === null) {
            return 0;
        }

        $locations = $this->resolveProviderQuery($service)
            ->get()
            ->map(function (ProviderLocation $location) use ($latitude, $longitude) {
                $distance = $this->calculateDistanceKm(
                    $latitude,
                    $longitude,
                    (float) $location->latitude,
                    (float) $location->longitude
                );

                $location->distance_km = $distance;
                $location->matching_score = $this->calculateMatchingScore($distance, $location->last_seen_at);

                return $location;
            })
            ->sortByDesc('matching_score')
            ->take(3)
            ->values();

        if ($locations->isEmpty()) {
            return 0;
        }

        $assignments = $locations->map(function (ProviderLocation $location) use ($service): array {
            return [
                'service_id' => $service->id,
                'provider_id' => $location->provider_id,
                'provider_type' => $this->resolveProviderTypeLabel($service),
                'distance_km' => round($location->distance_km, 2),
                'matching_score' => round($location->matching_score, 2),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        ServiceAssignment::insert($assignments);

        return count($assignments);
    }

    private function resolveProviderQuery(Service $service): Builder
    {
        $providerClass = $this->resolveProviderMorphType($service);

        $query = ProviderLocation::query()
            ->where('is_available', true)
            ->where('provider_type', $providerClass);

        if ($service->service_type === 'nurse') {
            $nurseMajor = $service->nurseService?->nurse_major;

            if ($nurseMajor) {
                $query->whereHasMorph(
                    'provider',
                    [Nurse::class],
                    fn (Builder $providerQuery) => $providerQuery->where('major', $nurseMajor)
                );
            }
        }

        return $query;
    }

    private function resolveMatchingCoordinates(Service $service): array
    {
        if ($service->service_type === 'driver') {
            return
            [
                $service->driverService?->pickup_latitude ? (float) $service->driverService->pickup_latitude : null,
                $service->driverService?->pickup_longitude ? (float) $service->driverService->pickup_longitude : null,
            ];
        }

        return [
            $service->service_latitude ? (float) $service->service_latitude : null,
            $service->service_longitude ? (float) $service->service_longitude : null,
        ];
    }

    private function resolveProviderTypeLabel(Service $service): string
    {
        return match ($service->service_type) {
            'nurse' => 'nurse',
            'driver' => 'driver',
            'companion' => 'companion',
            default => 'companion',
        };
    }

    private function resolveProviderMorphType(Service $service): string
    {
        return match ($service->service_type) {
            'nurse' => Nurse::class,
            'driver' => Driver::class,
            'companion' => Companion::class,
            default => Companion::class,
        };
    }

    private function calculateMatchingScore(float $distanceKm, ?string $lastSeenAt): float
    {
        $distanceScore = max(0, 100 - ($distanceKm * 10));

        if ($lastSeenAt === null) {
            return $distanceScore;
        }

        $minutesSinceLastSeen = now()->diffInMinutes($lastSeenAt);
        $recencyScore = max(0, 100 - $minutesSinceLastSeen);

        return ($distanceScore * 0.8) + ($recencyScore * 0.2);
    }

    private function calculateDistanceKm(
        float $originLatitude,
        float $originLongitude,
        float $targetLatitude,
        float $targetLongitude
    ): float {
        // Haversine estimates great-circle distance over Earth's surface.
        $earthRadiusKm = 6371;

        $latitudeDelta = deg2rad($targetLatitude - $originLatitude);
        $longitudeDelta = deg2rad($targetLongitude - $originLongitude);

        $a = sin($latitudeDelta / 2) * sin($latitudeDelta / 2)
            + cos(deg2rad($originLatitude))
            * cos(deg2rad($targetLatitude))
            * sin($longitudeDelta / 2)
            * sin($longitudeDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
