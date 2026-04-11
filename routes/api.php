<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\RegistrationController;

Route::prefix('v1')->group(function() 
{
    Route::post('/register', [RegistrationController::class, 'register']);
    Route::post('/nurse/register', [RegistrationController::class, 'nurseRegister']);
    Route::post('/companion/register', [RegistrationController::class, 'companionRegister']);
    Route::post('/driver/register', [RegistrationController::class, 'driverRegister']);
    Route::post('/family-member/register', [RegistrationController::class, 'familyMemberRegister']);
    Route::post('/elder/register', [RegistrationController::class, 'elderRegister']);
});
