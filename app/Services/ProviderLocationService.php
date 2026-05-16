<?php

namespace App\Services;

use App\Models\Companion;
use App\Models\Driver;
use App\Models\Nurse;

class ProviderLocationService
{
    public function updateLocation(float $latitude, float $longitude): array
    {
        $provider = $this->resolveProvider();

        if ($provider === null) {
            return [
                'status_code' => 403,
                'message' => 'Provider profile not found.',
            ];
        }

        $provider->location()->updateOrCreate(
            [],
            [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'last_seen_at' => now(),
            ]
        );

        return [
            'status_code' => 200,
            'message' => 'Location updated successfully.',
        ];
    }

    public function updateAvailability(bool $isAvailable): array
    {
        $provider = $this->resolveProvider();

        if ($provider === null) {
            return [
                'status_code' => 403,
                'message' => 'Provider profile not found.',
            ];
        }

        $provider->location()->updateOrCreate(
            [],
            [
                'is_available' => $isAvailable,
                'last_seen_at' => now(),
            ]
        );

        return [
            'status_code' => 200,
            'message' => 'Availability updated successfully.',
        ];
    }

    private function resolveProvider(): Nurse|Driver|Companion|null
    {
        $user = auth()->guard('api')->user();

        return match ($user->account_type) {
            'nurse' => Nurse::where('user_id', $user->id)->first(),
            'driver' => Driver::where('user_id', $user->id)->first(),
            'companion' => Companion::where('user_id', $user->id)->first(),
            default => null,
        };
    }
}
