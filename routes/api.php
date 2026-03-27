<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\RegistrationController;

Route::post('/register', [RegistrationController::class, 'store']);
Route::delete('/register/{id}', [RegistrationController::class, 'destroy']);