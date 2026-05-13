<?php

namespace App\Services\Auth;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function __construct(private EmailVerificationService $emailVerificationService) {}
    public function changePassword($request)
    {
        $user = auth()->guard('api')->user();

        if (!Hash::check($request->current_password, $user->password))
        {
            return [
                'status_code' => 401,
                'message' => 'Invalid current password',
            ];
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return 
        [
            'status_code' => 200,
            'message' => 'Password changed successfully.',
        ];
    }

    public function changePhoneNumber($request)
    {
        $user = auth()->guard('api')->user();

        $user->update([
            'phone_number' => $request->phone_number,
        ]);

        return [
            'status_code' => 200,
            'message' => 'Phone number changed successfully.',
        ];
    }

    public function changeEmail($request)
    {
        $user = auth()->guard('api')->user();

        if (! Hash::check($request->current_password, $user->password)) 
        {
            return [
                'status_code' => 401,
                'message' => 'Invalid current password',
            ];
        }

        DB::transaction(function () use ($user, $request) 
        {
            $user->update([
                'email' => $request->email,
                'email_verified_at' => null,
            ]);

            $this->emailVerificationService->sendVerificationCode($user);
        });

        return [
            'status_code' => 200,
            'message' => 'Email changed successfully. Please check your email for the verification code.',
        ];
    }

    public function uploadImage(UploadedFile $image): array
    {
        $user = auth()->guard('api')->user();

        if ($user->profile_image) 
        {
            Storage::disk('public')->delete($user->profile_image);
        }

        $path = $image->store('users/profile-images', 'public');

        $user->update(['profile_image' => $path]);

        return 
        [
            'status_code' => 200,
            'message' => 'Profile image uploaded successfully.',
            'profile_image_url' => asset('storage/' . $path),
        ];
    }

    public function deleteImage(): array
    {
        $user = auth()->guard('api')->user();

        if (!$user->profile_image) 
        {
            return 
            [
                'status_code' => 404,
                'message' => 'No profile image to delete.',
            ];
        }

        Storage::disk('public')->delete($user->profile_image);

        $user->update(['profile_image' => null]);

        return 
        [
            'status_code' => 200,
            'message' => 'Profile image deleted successfully.',
        ];
    }
}