<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Http\Requests\RegisterationRequest;
use App\Http\Requests\NurseRequest;
use App\Http\Requests\CompanionRequest;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\ElderRequest;
use App\Http\Requests\FamilyMemberRequest;
use App\Services\Auth\RegistrationService;

class RegistrationController
{
    public $registrationService;

    public function __construct(RegistrationService $registrationService) 
    {
        $this->registrationService = $registrationService;
    }
    
    private function handleCompleteRegistrationResponse($data, $key)
    {
        if ($data['status_code'] == 404) 
        {
            return response()->json([
                'message' => $data['message']
            ], $data['status_code']);
        }

        return response()->json([
            $key => $data[$key],
            'token' => $data['token'],
            'message' => $data['message']
        ], $data['status_code']);
    }

    public function register(RegisterationRequest $request)
    {
        $user = $this->registrationService->register($request);

        return response()->json([
            'user' => $user['user'],
            'message' => $user['message'], 
            'token' => $user['token']
        ], $user['status_code']);

        /*
            this function does not follow the same structure as the other registration functions
            because it does not generate a token and does not complete the registration process, it only creates the user 
            and returns a message to complete the profile 
        */
    }

    public function nurseRegister(NurseRequest $request)
    {
        $nurse = $this->registrationService->nurseRegister($request);

        return $this->handleCompleteRegistrationResponse($nurse, 'nurse');
    }

    public function companionRegister(CompanionRequest $request)
    {
        $companion = $this->registrationService->companionRegister($request);

        return $this->handleCompleteRegistrationResponse($companion, 'companion');
    }

    public function driverRegister(DriverRequest $request)
    {
        $driver = $this->registrationService->driverRegister($request);

        return $this->handleCompleteRegistrationResponse($driver, 'driver');
    }

    public function familyMemberRegister(FamilyMemberRequest $request)
    {
        $familyMember = $this->registrationService->familyMemberRegister($request);

        return $this->handleCompleteRegistrationResponse($familyMember, 'familyMember');
    }

    public function elderRegister(ElderRequest $request)
    {
        $elder = $this->registrationService->elderRegister($request);

        return $this->handleCompleteRegistrationResponse($elder, 'elderly');
    }
}
