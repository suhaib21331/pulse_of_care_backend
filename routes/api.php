<?php

use App\Http\Controllers\api\v1\Auth\LoginController;
use App\Http\Controllers\api\v1\Auth\RegistrationController;
use App\Http\Controllers\api\v1\BookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('auth:api')->post('/service', [BookingController::class, 'createBooking'])->middleware(['account.type:elderly,family-member']);

    Route::prefix('auth')->group(function () {
        Route::post('/register', [RegistrationController::class, 'register']);
        Route::post('/login', [LoginController::class, 'login']);

        Route::middleware('auth:api')->group(function () {
            Route::post('/nurse/register', [RegistrationController::class, 'nurseRegister'])->middleware(['account.type:nurse']);
            Route::post('/companion/register', [RegistrationController::class, 'companionRegister'])->middleware(['account.type:companion']);
            Route::post('/driver/register', [RegistrationController::class, 'driverRegister'])->middleware(['account.type:driver']);
            Route::post('/family-member/register', [RegistrationController::class, 'familyMemberRegister'])->middleware(['account.type:family-member']);
            Route::post('/elder/register', [RegistrationController::class, 'elderRegister'])->middleware(['account.type:elderly']);

            Route::get('/me', [LoginController::class, 'me']);
        });
    });
});
