<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Nurse;
use App\Models\Companion;
use App\Models\Driver;
use App\Models\FamilyMember;
use App\Models\Elder;

class RegisterationService
{
    public function register($request)
    {
        $user = User::create([
            'email' => $request->email,
            'password' => $request->password,
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'account_type' => $request->account_type
        ]);

        return $user;
    }

    public function nurseRegister($request)
    {
        $nurse = Nurse::create([
            'user_id' => $request->user_id,
            'major' => $request->major,
            'years_of_experience' => $request->years_of_experience,
            'license_number' => $request->license_number,
            'work_place' => $request->work_place,
            'about_you' => $request->about_you,
        ]);

        return $nurse;
    }

    public function companionRegister($request)
    {
        $companion = Companion::create([
            'user_id' => $request->user_id,
            'skills' => $request->skills,
            'years_of_experience' => $request->years_of_experience,
            'availability' => $request->availability,
            'certificates' => $request->certificates,
        ]);

        return $companion;
    }

    public function driverRegister($request)
    {
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

        return $driver;
    }

    public function familyMemberRegister($request)
    {
        $familyMember = FamilyMember::create([
            'user_id' => $request->user_id,
            'kinship' => $request->kinship,
            'elder_name' => $request->elder_name,
            'elder_age' => $request->elder_age,
            'city' => $request->city,
            'detailed_address' => $request->detailed_address,
            'notes' => $request->notes,
        ]);

        return $familyMember;

    }

    public function elderRegister($request)
    {
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

        return $elderlies;
    }
}