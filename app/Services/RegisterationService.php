<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Nurse;
use App\Models\Companion;


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

}