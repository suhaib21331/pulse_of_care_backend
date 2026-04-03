<?php

namespace Database\Seeders;

use App\Models\FamilyMember;
use App\Models\User;
use App\Models\Nurse;
use App\Models\Companion;
use App\Models\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'email' => 'test@example2.com',
            'password' => '123456',
            'full_name' => 'Test User',
            'phone_number' => '1234567890',
            'account_type' => 'nurse'
        ]);

        Nurse::create([
            'user_id' => User::first()->id,
            'major' => 'Pediatric Nursing',
            'years_of_experience' => "5",
            'license_number' => 23123456,
            'work_place' => 'City Hospital',
            'about_you' => 'Passionate about providing care to children.',
            'biometric' => random_bytes(32)
        ]); 
        
        Driver::create([
            'user_id' => User::first()->id,
            'driver_license_number' => 12345678,
            'car_license_number' => 12345678,
            'plate_number' => 1234,
            'car_company' => 'Toyota',
            'car_type' => 'Sedan',
            'year_of_creation' => 2015,
            'car_color' => 'Red',
            'biometric' => random_bytes(32)
        ]);

        Companion::create([
            'user_id' => User::first()->id,
            'skills' => 'Cooking, Cleaning, Companionship',
            'years_of_experience' => "3 سنوات",
            'availability' => 'صباح',
            'certificates' => 'First Aid, CPR',
            'biometric' => random_bytes(32)
        ]);
    }
}
