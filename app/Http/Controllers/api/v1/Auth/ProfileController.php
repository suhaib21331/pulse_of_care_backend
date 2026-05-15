<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangePhoneNumberRequest;
use App\Http\Requests\UploadProfileImageRequest;
use App\Services\Auth\ProfileService;
use Illuminate\Http\JsonResponse;

class ProfileController
{
    public function __construct(private ProfileService $profileService) {}

    public function changePassword(ChangePasswordRequest $request)
    {
        $result = $this->profileService->changePassword($request);

        return response()->json([
            'message' => $result['message'],
        ], $result['status_code']);
    }

    public function changePhoneNumber(ChangePhoneNumberRequest $request): JsonResponse
    {
        $result = $this->profileService->changePhoneNumber($request);

        return response()->json([
            'status_code' => $result['status_code'],
            'message' => $result['message'],
        ], $result['status_code']);
    }

    public function changeEmail(ChangeEmailRequest $request): JsonResponse
    {
        $result = $this->profileService->changeEmail($request);

        return response()->json([
            'message' => $result['message'],
            'status_code' => $result['status_code'],
        ], $result['status_code']);
    }

    public function uploadImage(UploadProfileImageRequest $request): JsonResponse
    {
        $result = $this->profileService->uploadImage($request->file('image'));

        return response()->json([
            'message' => $result['message'],
            'profile_image_url' => $result['profile_image_url'],
        ], $result['status_code']);
    }

    public function deleteImage(): JsonResponse
    {
        $result = $this->profileService->deleteImage();

        return response()->json([
            'message' => $result['message'],
        ], $result['status_code']);
    }
}
