<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\RegisterationRequest;
use App\Http\Requests\NurseRequest;
use App\Http\Requests\CompanionRequest;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\ElderRequest;
use App\Http\Requests\FamilyMemberRequest;
use Illuminate\Http\Request;
use App\Services\RegisterationService;

class RegistrationController
{
    public $registerationService;

    public function __construct(RegisterationService $registerationService) 
    {
        $this->registerationService = $registerationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function register(RegisterationRequest $request)
    {
        $user = $this->registerationService->register($request);

        return response()->json([
            'user' => $user,
            'message' => 'User registered successfully'
        ], 200);
    }

    public function nurseRegister(NurseRequest $request)
    {
        $nurse = $this->registerationService->nurseRegister($request);

        return response()->json([
            'nurse' => $nurse,
            'message' => 'Nurse registered successfully'
        ], 200);
    }

    public function companionRegister(CompanionRequest $request)
    {
        $companion = $this->registerationService->companionRegister($request);

        return response()->json([
            'companion' => $companion,
            'message' => 'Companion registered successfully'
        ], 200);
    }

    public function driverRegister(DriverRequest $request)
    {
        $driver = $this->registerationService->driverRegister($request);

        return response()->json([
            'driver' => $driver,
            'message' => 'Driver registered successfully'
        ], 200);
    }

    public function familyMemberRegister(Request $request)
    {
        $familyMember = $this->registerationService->familyMemberRegister($request);

        return response()->json([
            'family_member' => $familyMember,
            'message' => 'Family member registered successfully'
        ], 200);
    }

    public function elderRegister(ElderRequest $request)
    {
        $elder = $this->registerationService->elderRegister($request);

        return response()->json([
            'elder' => $elder,
            'message' => 'Elder registered successfully'
        ], 200);
    }
}
