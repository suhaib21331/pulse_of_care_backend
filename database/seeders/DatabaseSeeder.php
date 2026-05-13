<?php

namespace Database\Seeders;

use App\Models\Companion;
use App\Models\CompanionService;
use App\Models\Driver;
use App\Models\DriverService;
use App\Models\Elder;
use App\Models\FamilyMember;
use App\Models\Nurse;
use App\Models\NurseService;
use App\Models\ProviderLocation;
use App\Models\Service;
use App\Models\ServiceAssignment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $nurseUsers = $this->createUsers('nurse', 10);
        $driverUsers = $this->createUsers('driver', 10);
        $companionUsers = $this->createUsers('companion', 10);
        $elderUsers = $this->createUsers('elderly', 10);
        $familyUsers = $this->createUsers('family_member', 10);

        $nurses = $this->createNurses($nurseUsers);
        $drivers = $this->createDrivers($driverUsers);
        $companions = $this->createCompanions($companionUsers);

        $this->createElders($elderUsers);
        $this->createFamilyMembers($familyUsers);
        $this->createProviderLocations($nurses, $drivers, $companions);
        $this->createServicesAndAssignments($elderUsers, $nurses, $drivers, $companions);
    }

    private function createUsers(string $accountType, int $count): Collection
    {
        return collect(range(1, $count))->map(function (int $index) use ($accountType): User {
            return User::create([
                'email' => sprintf('%s_%02d@example.com', $accountType, $index),
                'password' => Hash::make('password123'),
                'full_name' => fake()->name(),
                'phone_number' => fake()->numberBetween(100000000, 999999999),
                'account_type' => $accountType,
                'is_profile_completed' => true,
            ]);
        });
    }

    private function createNurses(Collection $nurseUsers): Collection
    {
        $majors = ['geriatric', 'cardiac', 'neurology', 'orthopedic', 'critical_care'];

        return $nurseUsers->map(function (User $user) use ($majors): Nurse {
            return Nurse::create([
                'user_id' => $user->id,
                'major' => fake()->randomElement($majors),
                'years_of_experience' => (string) fake()->numberBetween(1, 15),
                'license_number' => fake()->numberBetween(100000, 999999),
                'work_place' => fake()->company(),
                'about_you' => fake()->sentence(12),
            ]);
        });
    }

    private function createDrivers(Collection $driverUsers): Collection
    {
        return $driverUsers->map(function (User $user): Driver {
            return Driver::create([
                'user_id' => $user->id,
                'driver_license_number' => fake()->numberBetween(100000, 999999),
                'car_license_number' => fake()->numberBetween(100000, 999999),
                'plate_number' => strtoupper(fake()->bothify('??-####')),
                'car_company' => fake()->randomElement(['Toyota', 'Hyundai', 'Kia', 'Honda']),
                'car_type' => fake()->randomElement(['Sedan', 'SUV', 'Van']),
                'useful_for_elder' => fake()->boolean(70),
                'year_of_creation' => fake()->numberBetween(2012, 2024),
                'car_color' => fake()->safeColorName(),
                'car_image' => null,
            ]);
        });
    }

    private function createCompanions(Collection $companionUsers): Collection
    {
        return $companionUsers->map(function (User $user): Companion {
            return Companion::create([
                'user_id' => $user->id,
                'skills' => implode(', ', fake()->words(4)),
                'years_of_experience' => (string) fake()->numberBetween(1, 10),
                'certificates' => implode(', ', fake()->words(3)),
            ]);
        });
    }

    private function createElders(Collection $elderUsers): void
    {
        $elderUsers->each(function (User $user): void {
            Elder::create([
                'user_id' => $user->id,
                'age' => fake()->numberBetween(60, 90),
                'gender' => fake()->randomElement(['male', 'female']),
                'chronic_diseases' => fake()->sentence(),
                'current_medications' => fake()->sentence(),
                'allergies' => fake()->sentence(),
                'need_wheel_chair' => fake()->boolean(),
                'uses_diapers' => fake()->boolean(),
                'movement_level' => fake()->randomElement(['low', 'medium', 'high']),
                'city' => fake()->city(),
                'detailed_address' => fake()->address(),
                'notes' => fake()->sentence(),
            ]);
        });
    }

    private function createFamilyMembers(Collection $familyUsers): void
    {
        $familyUsers->each(function (User $user): void {
            FamilyMember::create([
                'user_id' => $user->id,
                'kinship' => fake()->randomElement(['son', 'daughter', 'brother', 'sister']),
                'elder_name' => fake()->name(),
                'elder_age' => fake()->numberBetween(60, 92),
                'city' => fake()->city(),
                'need_wheel_chair' => fake()->boolean(),
                'health_condition' => fake()->sentence(),
                'uses_diapers' => fake()->boolean(),
                'detailed_address' => fake()->address(),
                'notes' => fake()->sentence(),
            ]);
        });
    }

    private function createProviderLocations(Collection $nurses, Collection $drivers, Collection $companions): void
    {
        $this->createLocationsForProviders($nurses, Nurse::class);
        $this->createLocationsForProviders($drivers, Driver::class);
        $this->createLocationsForProviders($companions, Companion::class);
    }

    private function createLocationsForProviders(Collection $providers, string $providerType): void
    {
        $providers->each(function ($provider) use ($providerType): void {
            ProviderLocation::create([
                'provider_id' => $provider->id,
                'provider_type' => $providerType,
                'latitude' => fake()->latitude(29.90, 31.50),
                'longitude' => fake()->longitude(30.80, 32.60),
                'is_available' => fake()->boolean(85),
                'last_seen_at' => now()->subMinutes(fake()->numberBetween(1, 60)),
            ]);
        });
    }

    private function createServicesAndAssignments(
        Collection $elderUsers,
        Collection $nurses,
        Collection $drivers,
        Collection $companions
    ): void {
        $types = ['nurse', 'driver', 'companion'];
        $nurseMajors = $nurses->pluck('major')->unique()->values()->all();

        foreach (range(1, 30) as $index) {
            $serviceType = $types[$index % 3];
            $elder = $elderUsers->random();

            $service = Service::create([
                'elder_id' => $elder->id,
                'service_type' => $serviceType,
                'service_condition' => fake()->randomElement(['normal', 'urgent', 'emergency']),
                'service_address' => $serviceType === 'driver' ? null : fake()->address(),
                'service_latitude' => $serviceType === 'driver' ? null : fake()->latitude(29.90, 31.50),
                'service_longitude' => $serviceType === 'driver' ? null : fake()->longitude(30.80, 32.60),
                'status' => 'pending',
            ]);

            if ($serviceType === 'nurse') {
                NurseService::create([
                    'service_id' => $service->id,
                    'nurse_major' => fake()->randomElement($nurseMajors),
                ]);
            }

            if ($serviceType === 'driver') {
                DriverService::create([
                    'service_id' => $service->id,
                    'pickup_address' => fake()->address(),
                    'pickup_latitude' => fake()->latitude(29.90, 31.50),
                    'pickup_longitude' => fake()->longitude(30.80, 32.60),
                    'dropoff_address' => fake()->address(),
                    'dropoff_latitude' => fake()->latitude(29.90, 31.50),
                    'dropoff_longitude' => fake()->longitude(30.80, 32.60),
                ]);
            }

            if ($serviceType === 'companion') {
                CompanionService::create([
                    'service_id' => $service->id,
                    'start_time' => fake()->randomElement(['08:00:00', '10:00:00', '14:00:00']),
                    'end_time' => fake()->randomElement(['12:00:00', '16:00:00', '20:00:00']),
                    'period' => fake()->randomElement(['morning', 'evening', 'full_day']),
                ]);
            }

            $providers = match ($serviceType) {
                'nurse' => $nurses,
                'driver' => $drivers,
                default => $companions,
            };

            $providerType = $serviceType;
            $assignments = $providers
                ->random(3)
                ->map(function ($provider) use ($service, $providerType): array {
                    return [
                        'service_id' => $service->id,
                        'provider_id' => $provider->id,
                        'provider_type' => $providerType,
                        'distance_km' => fake()->randomFloat(2, 0.5, 25),
                        'matching_score' => fake()->randomFloat(2, 60, 99),
                        'status' => fake()->randomElement(['pending', 'accepted', 'rejected']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->all();

            ServiceAssignment::insert($assignments);

            $service->update(['status' => 'assigned']);
        }
    }
}
