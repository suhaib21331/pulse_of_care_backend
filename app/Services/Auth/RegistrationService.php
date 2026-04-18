<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Nurse;
use App\Models\Companion;
use App\Models\Driver;
use App\Models\FamilyMember;
use App\Models\Elder;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegistrationService
{
    public function register($request)
    {
        $user = User::create([
            'email' => $request->email,
            'password' => $request->password,
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'account_type' => $request->account_type,
            'is_profile_completed' => $request->is_profile_completed
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
            'message' => 'User registered successfully',
            'status_code' => 200
        ];
    }

    public function nurseRegister($request)
    {
        $user = auth()->guard('api')->user();

        if (Nurse::where('user_id', $user->id)->exists()) 
        {
            return [
                'message' => 'Nurse profile already exists',
                'status_code' => 400
            ];
        }

        $nurse = Nurse::create([
            'user_id' => $user->id,
            'major' => $request->major,
            'years_of_experience' => $request->years_of_experience,
            'license_number' => $request->license_number,
            'work_place' => $request->work_place,
            'about_you' => $request->about_you,
            
        ]);

        $user->update([
            'is_profile_completed' => true
        ]);

        return 
        [
            'nurse' => $nurse,
            'message' => 'Nurse registered successfully',
            'status_code' => 200   
        ];
    }

    public function companionRegister($request)
    {
        $user = auth()->guard('api')->user();

        if (Companion::where('user_id', $user->id)->exists()) 
        {
            return [
                'message' => 'Companion profile already exists',
                'status_code' => 400
            ];
        }

        $companion = Companion::create([
            'user_id' => $user->id,
            'skills' => $request->skills,
            'years_of_experience' => $request->years_of_experience,
            'availability' => $request->availability,
            'certificates' => $request->certificates,
        ]);

        $user->update([
            'is_profile_completed' => true
        ]);

        return 
        [
            'companion' => $companion,
            'message' => 'Companion registered successfully',
            'status_code' => 200
        ];
    }

    public function driverRegister($request)
    {
        $user = auth()->guard('api')->user();

        if (Driver::where('user_id', $user->id)->exists()) 
        {
            return [
                'message' => 'Driver profile already exists',
                'status_code' => 400
            ];
        }

        $carImagePath = null;

        if ($request->hasFile('car_image')) 
        {
            $carImagePath = $request->file('car_image')->store('drivers/cars', 'public');
        }

        $driver = Driver::create([
            'user_id' => $user->id,
            'driver_license_number' => $request->driver_license_number,
            'car_type' => $request->car_type,
            'car_color' => $request->car_color,
            'car_company' => $request->car_company,
            'year_of_creation' => $request->year_of_creation,
            'car_license_number' => $request->car_license_number,
            'plate_number' => $request->plate_number,
            'car_image' => $carImagePath,
        ]);

        $user->update([
            'is_profile_completed' => true
        ]);

        return 
        [
            'driver' => [
                ...$driver->toArray(),
                'car_image_url' => $driver->car_image 
                    ? asset('storage/' . $driver->car_image)
                    : null,
            ],
            'message' => 'Driver registered successfully',
            'status_code' => 200
        ];
    }

    public function familyMemberRegister($request)
    {
        $user = auth()->guard('api')->user();

        if (FamilyMember::where('user_id', $user->id)->exists()) 
        {
            return [
                'message' => 'Family member profile already exists',
                'status_code' => 400
            ];
        }

        $familyMember = FamilyMember::create([
            'user_id' => $user->id,
            'kinship' => $request->kinship,
            'elder_name' => $request->elder_name,
            'elder_age' => $request->elder_age,
            'city' => $request->city,
            'detailed_address' => $request->detailed_address,
            'notes' => $request->notes,
        ]);

        $user->update([
            'is_profile_completed' => true
        ]);

        return 
        [
            'familyMember' => $familyMember,
            'message' => 'Family member registered successfully',
            'status_code' => 200
        ];
    }

    public function elderRegister($request)
    {
        $user = auth()->guard('api')->user();

        if (Elder::where('user_id', $user->id)->exists()) 
        {
            return [
                'message' => 'Elder profile already exists',
                'status_code' => 400
            ];
        }

        $elderlies = Elder::create([
            'user_id' => $user->id,
            'gender' => $request->gender,
            'chronic_diseases' => $request->chronic_diseases,
            'current_medications' => $request->current_medications,
            'allergies' => $request->allergies,
            'can_walk' => $request->can_walk,
            'need_wheel_chair' => $request->need_wheel_chair,
            'city' => $request->city,
            'detailed_address' => $request->detailed_address,
            'notes' => $request->notes,
            'age' => $request->age,
        ]);

        $user->update([
            'is_profile_completed' => true
        ]);

        return 
        [
            'elderly' => $elderlies,
            'message' => 'Elderly registered successfully',
            'status_code' => 200
        ];
    }

}