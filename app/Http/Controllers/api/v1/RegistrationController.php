<?php

namespace App\Http\Controllers\api\v1;

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
    public function register(Request $request)
    {
        return $this->registerationService->register($request);
    }
}
