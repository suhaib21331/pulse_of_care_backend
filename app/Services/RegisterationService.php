<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\User;

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

        return $user->id;
    }

}