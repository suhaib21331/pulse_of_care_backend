<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\Auth\LoginController;
use App\Http\Controllers\api\v1\Auth\RegistrationController;

Route::prefix('v1')->group(function() 
{
    Route::prefix('auth')->group(function()
    {
        Route::post('/register', [RegistrationController::class, 'register']);
        Route::post('/login', [LoginController::class, 'login']);
        Route::post('/nurse/register', [RegistrationController::class, 'nurseRegister']);
        Route::post('/companion/register', [RegistrationController::class, 'companionRegister']);
        Route::post('/driver/register', [RegistrationController::class, 'driverRegister']);
        Route::post('/family-member/register', [RegistrationController::class, 'familyMemberRegister']);
        Route::post('/elder/register', [RegistrationController::class, 'elderRegister']);
    });
    
    Route::get('/test', function() {
        return response()->json([
            'message' => 'Hello World'
        ], 200);
    });
});
