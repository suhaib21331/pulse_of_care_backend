<?php

use App\Http\Controllers\api\v1\Auth\LoginController;
use App\Http\Controllers\api\v1\Auth\ProfileController;
use App\Http\Controllers\api\v1\Auth\RegistrationController;
use App\Http\Controllers\api\v1\BookingController;
use App\Http\Controllers\api\v1\ProviderOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('auth:api')->post('/service', [BookingController::class, 'createBooking'])->middleware(['account.type:elderly,family-member']);

    Route::prefix('auth')->group(function () {
        Route::post('/register', [RegistrationController::class, 'register']);
        Route::post('/login', [LoginController::class, 'login']);

        Route::middleware('auth:api')->group(function () {
            Route::post('/email/verify', [RegistrationController::class, 'verifyEmail']);
            Route::get('/email/resend', [RegistrationController::class, 'resendCode']);
            Route::post('/nurse/register', [RegistrationController::class, 'nurseRegister'])->middleware(['account.type:nurse']);
            Route::post('/companion/register', [RegistrationController::class, 'companionRegister'])->middleware(['account.type:companion']);
            Route::post('/driver/register', [RegistrationController::class, 'driverRegister'])->middleware(['account.type:driver']);
            Route::post('/family_member/register', [RegistrationController::class, 'familyMemberRegister'])->middleware(['account.type:family_member']);
            Route::post('/elder/register', [RegistrationController::class, 'elderRegister'])->middleware(['account.type:elderly']);

            Route::get('/me', [LoginController::class, 'me']);

            Route::prefix('profile')->group(function () {
                Route::post('/change-password', [ProfileController::class, 'changePassword'])->middleware('email.verified');
                Route::post('/change-email', [ProfileController::class, 'changeEmail'])->middleware('email.verified');
                Route::post('/change-phone-number', [ProfileController::class, 'changePhoneNumber'])->middleware('email.verified');
                Route::post('/image', [ProfileController::class, 'uploadImage'])->middleware(['email.verified', 'profile.completed']);
                Route::delete('/image', [ProfileController::class, 'deleteImage'])->middleware(['email.verified', 'profile.completed']);
            });
        });
    });

    Route::prefix('provider')->middleware(['auth:api', 'email.verified', 'profile.completed'])->group(function () {
        Route::prefix('orders')->middleware(['account.type:nurse,driver,companion'])->group(function () {
            Route::get('/', [ProviderOrderController::class, 'index']);
            Route::post('/{assignment}/accept', [ProviderOrderController::class, 'accept']);
            Route::post('/{assignment}/reject', [ProviderOrderController::class, 'reject']);
        });
    });
});
