<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\RegisterationRequest;
use App\Http\Requests\NurseRequest;
use App\Http\Requests\CompanionRequest;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\ElderRequest;
use App\Http\Requests\FamilyMemberRequest;
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

        if(!isset($nurse['token']) && !isset($nurse['nurse'])) 
        {
            return response()->json([
                'message' => $nurse['message']
            ], 404);
        }
        
        return response()->json([
            'nurse' => $nurse,
            'token' => $nurse['token'],
            'message' => 'Nurse registered successfully'
        ], 200);
    }

    public function companionRegister(CompanionRequest $request)
    {
        $companion = $this->registerationService->companionRegister($request);

        if(!isset($companion['token']) && !isset($companion['companion'])) 
        {
            return response()->json([
                'message' => $companion['message']
            ], 404);
        }

        return response()->json([
            'companion' => $companion,
            'token' => $companion['token'],
            'message' => 'Companion registered successfully'
        ], 200);
    }

    public function driverRegister(DriverRequest $request)
    {
        $driver = $this->registerationService->driverRegister($request);

        if(!isset($driver['token']) && !isset($driver['driver'])) 
        {
            return response()->json([
                'message' => $driver['message']
            ], 404);
        }

        return response()->json([
            'driver' => $driver,
            'token' => $driver['token'],
            'message' => 'Driver registered successfully'
        ], 200);
    }

    public function familyMemberRegister(FamilyMemberRequest $request)
    {
        $familyMember = $this->registerationService->familyMemberRegister($request);

        if(!isset($familyMember['token']) && !isset($familyMember['familyMember'])) 
        {
            return response()->json([
                'message' => $familyMember['message']
            ], 404);
        }

        return response()->json([
            'familyMember' => $familyMember['familyMember'],
            'token' => $familyMember['token'],
            'message' => $familyMember['message']
        ], 200);
    }

    public function elderRegister(ElderRequest $request)
    {
        $elder = $this->registerationService->elderRegister($request);

        if(!isset($elder['token']) && !isset($elder['elder'])) 
        {
            return response()->json([
                'message' => $elder['message']
            ], 404);
        }

        return response()->json([
            'elder' => $elder,
            'token' => $elder['token'],
            'message' => 'Elder registered successfully'
        ], 200);
    }
}
