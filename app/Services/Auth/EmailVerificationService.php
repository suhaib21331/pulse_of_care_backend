<?php

namespace App\Services\Auth;

use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmailVerificationService
{
    public function sendVerificationCode(User $user): void
    {
        $code = (string) random_int(100000, 999999);
        
        $user->update([
            'email_verification_code' => Hash::make($code),
            'email_verification_expires_at' => now()->addMinutes(10),
        ]);

        SendEmailVerificationJob::dispatch($user, $code, $user->email);
    }

    public function verifyCode(string $code): array
    {
        $user = auth()->guard('api')->user();

        if ($user->email_verified_at) 
        {
            return 
            [
                'status_code' => 409,
                'message' => 'Email is already verified.',
            ];
        }

        if (!$user->email_verification_code || !Hash::check($code, $user->email_verification_code)) 
        {
            return 
            [
                'status_code' => 422,
                'message' => 'Invalid verification code.',
            ];
        }

        if (now()->isAfter($user->email_verification_expires_at)) 
        {
            return 
            [
                'status_code' => 422,
                'message' => 'Verification code has expired.',
            ];
        }

      $user->update([
    'email_verified_at' => now(),
    'email_verification_code' => null,
    'email_verification_expires_at' => null,
]);

$user = $user->fresh();

$accessToken = JWTAuth::fromUser($user);

return 
[
    'status_code' => 200,
    'message' => 'Email verified successfully.',
    'token' => $accessToken,
    'user' => [
        'id' => $user->id,
        'email' => $user->email,
        'full_name' => $user->full_name,
        'phone_number' => $user->phone_number,
        'account_type' => $user->account_type,
        'is_profile_completed' => $user->is_profile_completed,
        'email_verified_at' => $user->email_verified_at,
    ],
];
    }

    public function resendCode(): array
    {
        $user = auth()->guard('api')->user();

        if (!$user) 
        {
            return 
            [
                'status_code' => 404,
                'message' => 'User not found.',
            ];
        }

        if ($user->email_verified_at) 
        {
            return 
            [
                'status_code' => 409,
                'message' => 'Email is already verified.',
            ];
        }

        $this->sendVerificationCode($user);

        return 
        [
            'status_code' => 200,
            'message' => 'Verification code resent successfully.',
        ];
    }
}
