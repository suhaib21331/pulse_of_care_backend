<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\RegistrationController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [RegistrationController::class, 'register']);
    Route::post('/nurse/register', [RegistrationController::class, 'nurseRegister']);
});
