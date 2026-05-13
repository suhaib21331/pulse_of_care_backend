<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Http\Requests\CompanionRequest;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\ElderRequest;
use App\Http\Requests\FamilyMemberRequest;
use App\Http\Requests\NurseRequest;
use App\Http\Requests\RegisterationRequest;
use App\Http\Requests\VerifyEmailCodeRequest;
use App\Services\Auth\EmailVerificationService;
use App\Services\Auth\RegistrationService;
use Illuminate\Http\JsonResponse;

class RegistrationController
{
    public function __construct(
        private RegistrationService $registrationService,
        private EmailVerificationService $emailVerificationService,
    ) {}

    private function handleCompleteRegistrationResponse(array $data, string $key): JsonResponse
    {
        if ($data['status_code'] !== 200)
        {
            return response()->json([
                'message' => $data['message'],
            ], $data['status_code']);
        }

        return response()->json([
            $key => $data[$key],
            'message' => $data['message'],
        ], $data['status_code']);
    }

    public function register(RegisterationRequest $request): JsonResponse
    {
        $result = $this->registrationService->register($request);

        return response()->json([
            'user' => $result['user'],
            'message' => $result['message'],
            'token' => $result['token'],
        ], $result['status_code']);
    }

    public function verifyEmail(VerifyEmailCodeRequest $request): JsonResponse
    {
        $result = $this->emailVerificationService->verifyCode(
            $request->validated('code'),
        );

        if ($result['status_code'] !== 200)
        {
            return response()->json([
                'message' => $result['message'],
            ], $result['status_code']);
        }

        return response()->json([
            'token' => $result['token'],
            'user' => $result['user'],
            'message' => $result['message'],
        ], 200);
    }

    public function resendCode(): JsonResponse
    {
        $result = $this->emailVerificationService->resendCode();

        return response()->json([
            'message' => $result['message'],
        ], $result['status_code']);
    }

    public function nurseRegister(NurseRequest $request): JsonResponse
    {
        $nurse = $this->registrationService->nurseRegister($request);

        return $this->handleCompleteRegistrationResponse($nurse, 'nurse');
    }

    public function companionRegister(CompanionRequest $request): JsonResponse
    {
        $companion = $this->registrationService->companionRegister($request);

        return $this->handleCompleteRegistrationResponse($companion, 'companion');
    }

    public function driverRegister(DriverRequest $request): JsonResponse
    {
        $driver = $this->registrationService->driverRegister($request);

        return $this->handleCompleteRegistrationResponse($driver, 'driver');
    }

    public function familyMemberRegister(FamilyMemberRequest $request): JsonResponse
    {
        $familyMember = $this->registrationService->familyMemberRegister($request);

        return $this->handleCompleteRegistrationResponse($familyMember, 'familyMember');
    }

    public function elderRegister(ElderRequest $request): JsonResponse
    {
        $elder = $this->registrationService->elderRegister($request);

        return $this->handleCompleteRegistrationResponse($elder, 'elderly');
    }
}
