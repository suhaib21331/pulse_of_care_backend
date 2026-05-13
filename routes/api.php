<?php

use App\Http\Controllers\api\v1\Auth\LoginController;
use App\Http\Controllers\api\v1\Auth\RegistrationController;
use App\Http\Controllers\api\v1\BookingController;
use App\Http\Controllers\api\v1\Auth\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () 
{
    Route::middleware('auth:api')->post('/service', [BookingController::class, 'createBooking'])->middleware(['account.type:elderly,family-member']);

    Route::prefix('auth')->group(function () 
    {
        Route::post('/register', [RegistrationController::class, 'register']);
        Route::post('/login', [LoginController::class, 'login']);

        Route::middleware('auth:api')->group(function () 
        {
            Route::post('/email/verify', [RegistrationController::class, 'verifyEmail']);
            Route::get('/email/resend', [RegistrationController::class, 'resendCode']);
            Route::post('/nurse/register', [RegistrationController::class, 'nurseRegister'])->middleware(['account.type:nurse']);
            Route::post('/companion/register', [RegistrationController::class, 'companionRegister'])->middleware(['account.type:companion']);
            Route::post('/driver/register', [RegistrationController::class, 'driverRegister'])->middleware(['account.type:driver']);
            Route::post('/family-member/register', [RegistrationController::class, 'familyMemberRegister'])->middleware(['account.type:family-member']);
            Route::post('/elder/register', [RegistrationController::class, 'elderRegister'])->middleware(['account.type:elderly']);

            Route::get('/me', [LoginController::class, 'me']);

            Route::prefix('profile')->group(function () 
            {
                Route::post('/change-password', [ProfileController::class, 'changePassword'])->middleware('email.verified');
                Route::post('/change-email', [ProfileController::class, 'changeEmail'])->middleware('email.verified');
                Route::post('/change-phone-number', [ProfileController::class, 'changePhoneNumber'])->middleware('email.verified');
                Route::post('/image', [ProfileController::class, 'uploadImage'])->middleware(['email.verified', 'profile.completed']);
                Route::delete('/image', [ProfileController::class, 'deleteImage'])->middleware(['email.verified', 'profile.completed']);
            });
        });
    });
});
