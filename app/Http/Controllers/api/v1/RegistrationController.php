<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\RegisterationRequest;
use App\Http\Requests\NurseRequest;
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
}
