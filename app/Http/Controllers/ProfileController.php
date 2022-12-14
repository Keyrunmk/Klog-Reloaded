<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        return $this->successResponse(message: "Profile id: $profile_id", data: new ProfileResource($profile));
    }

    public function update(int $profile_id, Request $request): JsonResponse
    {
        try {
            $profile = $this->profileService->find($profile_id);
            $this->authorize("update", $profile);
            $profile = $this->profileService->update($profile, $request);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("No profile with id: $profile_id", (int) $exception->getCode());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Profile updated", data: new ProfileResource($profile));
    }

    public function follow(int $profile_id): JsonResponse
    {
        try {
            $response = $this->profileService->followProfile($profile_id);
        } catch (NotFoundException $exception) {
            return $this->errorResponse("Couldn't find the profile to follow", (int) $exception->getCode());
        } catch (Exception $exception) {
            return $this->errorResponse("Something went wrong", (int)$exception->getCode());
        }

        return $this->successResponse($response);
    }
}
