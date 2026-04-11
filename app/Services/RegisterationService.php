<?php

namespace App\Services;

use App\Models\User;
use App\Models\Nurse;
use App\Models\Companion;
use App\Models\Driver;
use App\Models\FamilyMember;
use App\Models\Elder;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterationService
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

        return $user;
    }

    public function nurseRegister($request)
    {
        $user = User::where('id', $request->user_id)
        ->where('account_type', 'nurse')
        ->where('is_profile_completed', false)
        ->first();

        if(!$user) 
        {
            return [
                'message' => 'User not found or profile already completed'
            ];
        }

        $nurse = Nurse::create([
            'user_id' => $request->user_id,
            'major' => $request->major,
            'years_of_experience' => $request->years_of_experience,
            'license_number' => $request->license_number,
            'work_place' => $request->work_place,
            'about_you' => $request->about_you,
        ]);

        $user->update([
            'is_profile_completed' => true
        ]);

        $token = JWTAuth::fromUser($user);

        return 
        [
            'nurse' => $nurse,
            'token' => $token        
        ];
    }

    public function companionRegister($request)
    {
        $user = User::where('id', $request->user_id)
        ->where('account_type', 'companion')
        ->where('is_profile_completed', false)
        ->first();

        if(!$user) 
        {
            return 
            [
                'message' => 'User not found or profile already completed'
            ];
        }

        $companion = Companion::create([
            'user_id' => $request->user_id,
            'skills' => $request->skills,
            'years_of_experience' => $request->years_of_experience,
            'availability' => $request->availability,
            'certificates' => $request->certificates,
        ]);

        $user->update([
            'is_profile_completed' => true
        ]);

        $token = JWTAuth::fromUser($user);

        return 
        [
            'companion' => $companion,
            'token' => $token,
            'message' => 'Companion registered successfully'
        ];
    }

    public function driverRegister($request)
    {
        $user = User::where('id', $request->user_id)
        ->where('account_type', 'driver')
        ->where('is_profile_completed', false)
        ->first();

        if(!$user) {
            return 
            [
                'message' => 'User not found or profile already completed'
            ];
        }

        $driver = Driver::create([
            'user_id' => $request->user_id,
            'driver_license_number' => $request->driver_license_number,
            'car_type' => $request->car_type,
            'car_color' => $request->car_color,
            'car_company' => $request->car_company,
            'year_of_creation' => $request->year_of_creation,
            'car_license_number' => $request->car_license_number,
            'plate_number' => $request->plate_number,
        ]);

        $user->update([
            'is_profile_completed' => true
        ]);

        $token = JWTAuth::fromUser($user);

        return 
        [
            'driver' => $driver,
            'token' => $token,
            'message' => 'Driver registered successfully'
        ];
    }

    public function familyMemberRegister($request)
    {
        $user = User::where('id', $request->user_id)
        ->where('account_type', 'family_member')
        ->where('is_profile_completed', false)
        ->first();

        if(!$user) 
        {
            return 
            [
                'message' => 'User not found or profile already completed'
            ];
        }

        $familyMember = FamilyMember::create([
            'user_id' => $request->user_id,
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

        $token = JWTAuth::fromUser($user);

        return 
        [
            'familyMember' => $familyMember,
            'token' => $token,
            'message' => 'Family member registered successfully'
        ];
    }

    public function elderRegister($request)
    {
        $user = User::where('id', $request->user_id)
        ->where('account_type', 'elderly')
        ->where('is_profile_completed', false)
        ->first();

        if(!$user) 
        {
            return 
            [
                'message' => 'User not found or profile already completed'
            ];
        }

        $elderlies = Elder::create([
            'user_id' => $request->user_id,
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

        $token = JWTAuth::fromUser($user);

        return 
        [
            'elderly' => $elderlies,
            'token' => $token
        ];
    }
}