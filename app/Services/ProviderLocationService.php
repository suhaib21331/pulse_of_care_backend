<?php

namespace App\Services;

use App\Models\Companion;
use App\Models\Driver;
use App\Models\Nurse;
use Illuminate\Support\Facades\Log;

class ProviderLocationService
{
    public function updateLocation(float $latitude, float $longitude): array
    {
        $user = auth()->guard('api')->user();
        $provider = $this->resolveProvider($user);

        Log::info('Provider location update', [
            'user_id' => $user->id,
            'account_type' => $user->account_type,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        if ($provider === null) {
            return [
                'status_code' => 404,
                'message' => 'Provider profile not found.',
            ];
        }

        $provider->location()->updateOrCreate(
            [],
            [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'is_available' => true,
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
        $user = auth()->guard('api')->user();
        $provider = $this->resolveProvider($user);

        Log::info('Provider availability update', [
            'user_id' => $user->id,
            'account_type' => $user->account_type,
            'is_available' => $isAvailable,
        ]);

        if ($provider === null) {
            return [
                'status_code' => 404,
                'message' => 'Provider profile not found.',
            ];
        }

        $location = $provider->location;

        if ($location === null) {
            // No location row yet — latitude/longitude are NOT NULL in the DB so we
            // cannot create a row here. Turning off availability on a provider that
            // has never shared their location is a no-op; treat it as success.
            return [
                'status_code' => 200,
                'message' => 'Availability updated successfully.',
            ];
        }

        $location->update([
            'is_available' => $isAvailable,
            'last_seen_at' => now(),
        ]);

        return [
            'status_code' => 200,
            'message' => 'Availability updated successfully.',
        ];
    }

    private function resolveProvider(mixed $user): Nurse|Driver|Companion|null
    {
        return match ($user->account_type) {
            'nurse' => Nurse::where('user_id', $user->id)->first(),
            'driver' => Driver::where('user_id', $user->id)->first(),
            'companion' => Companion::where('user_id', $user->id)->first(),
            default => null,
        };
    }
}
