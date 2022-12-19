<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Services\ProfileService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileController extends BaseController
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show(int $profile_id): JsonResponse
    {
        try {
            $profile = $this->profileService->find($profile_id);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Profile id: $profile_id", data: $this->resource($profile));
    }

    public function update(int $profile_id, ProfileRequest $request): JsonResponse
    {
        try {
            $profile = $this->profileService->find($profile_id);
            $this->authorize("update", $profile);
            $profile = $this->profileService->update($profile, $request->all());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Profile updated", data: $this->resource($profile));
    }

    public function follow(int $profile_id): JsonResponse
    {
        try {
            $response = $this->profileService->followProfile($profile_id);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse($response);
    }

    public function resource(Profile $data): JsonResource
    {
        return new ProfileResource($data);
    }
}
